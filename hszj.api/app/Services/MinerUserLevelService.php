<?php


namespace App\Services;


use App\Utils\Snowflake;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * 等级管理
 * Class MinerUserLevelService
 * @package App\Services
 */
class MinerUserLevelService extends Service
{

    /**
     * 获取等级列表
     * @param $user_id
     * @return Collection
     */
    public function levelList($user_id)
    {
        $userLevel = $this->getUserLevel($user_id);
        $level = 0;
        if (!empty($userLevel)) {
            $level = $userLevel->level;
        }
        return DB::table('miner_level')
            ->where('is_delete', 0)
            ->where('level', '>', $level)
            ->where('is_disable', 0)
            ->orderBy('level', 'desc') //倒叙排序 从高等级往下匹配 不可更改
            ->select(['id', 'level', 'nickname', 'rule', 'is_audit', 'dividend_ratio', 'reward', 'is_pop_up'])
            ->get();
    }

    /**
     * 获取用户等级
     * @param $user_id
     * @return Model|Builder|object|null
     */
    public function getUserLevel($user_id)
    {
        $level = DB::table('miner_user_level as mul')
            ->join('miner_level as ml', 'mul.miner_level_id', 'ml.level')
            ->where([
                ['mul.user_id', '=', $user_id],
                ['mul.is_delete', '=', 0],
                ['ml.is_disable', '=', 0],
                ['mul.is_delete', '=', 0],
                ['mul.is_disable', '=', 0]
            ])->select(['mul.id', 'mul.is_audit', 'ml.icon', 'mul.is_issue', 'ml.level', 'ml.nickname', 'ml.rule', 'ml.dividend_ratio', 'ml.reward', 'mul.is_pop_up'])
            ->orderBy('mul.create_time', 'desc')
            ->first();
        if ($level) {
            $level->id .= '';
            if ($level->icon) {
                $level->icon = getDomain() . $level->icon;
                $level->info = "恭喜您已达到{$level->nickname}等级,请再接再厉!";
            }
            if (!$level->is_audit) {
                //只弹出一次
                DB::table('miner_user_level')->where('id', $level->id)->update(['is_pop_up' => 0]);
            }
        }
        return $level;
    }

    /**
     * 确定用户等级
     * @param $user_id
     * @return mixed|null
     */
    public function isUserLevel($user_id)
    {
        $levelList = $this->levelList($user_id);
        //计算团队兑换树人数

        $computing_power = DB::table('miner_user_team_info')->where('user_id', $user_id)->value('total_computing_power');
        $userLevel = null;
        foreach ($levelList as $item) {
            if (empty($item->rule)) {
                continue;
            }

            $rule = json_decode($item->rule, true);
            if (!is_array($rule) || empty($rule)) {
                continue;
            }

            $user_direct_count = 0;
            //直推人数
            if ($rule['level_id'] > 0) { //说明直推的是等级人数
                $user_temp = DB::table('Members')->where('ParentId', $user_id)->pluck('Id') ?? [];
                if (!empty($user_temp)) {
                    $user_direct_count = DB::table('miner_user_level')
                        ->whereIn('user_id', $user_temp)
                        ->where('miner_level_id','>=' , $rule['level_id'])->count('id');
                }
            } else {
                $child = DB::table('Members')
                    ->where('ParentId', $user_id)
                    ->where('IsAuth', 1)
                    ->pluck('Id');
                $user_direct_count = count($child);
            }

            if ($user_direct_count >= $rule['direct_push'] && $computing_power >= $rule['computing_power']) {
                $userLevel = $item;
                break;
            }
        }
        return $userLevel;
    }

    /**
     * 更新用户等级
     * @param $user_id
     * @return bool
     * @throws \Throwable
     */
    public function settingUserLevel($user_id)
    {
        $userLevel = $this->isUserLevel($user_id);
        if (empty($userLevel)) {
            return false;
        }

        $oldUserLevel = $this->getUserLevel($user_id);
        if (!empty($oldUserLevel)) {
            if ($oldUserLevel->level >= $userLevel->level) {
                return false;
            } else {
                $is_pop_up = 0;
                $is_audit = 0;
                $is_dividend = 0;
                $is_reward = 0;
                if ($userLevel->is_pop_up) {
                    $is_pop_up = 1;
                }
                if ($userLevel->is_audit) {
                    $is_audit = 1;
                }
                if ($userLevel->dividend_ratio > 0) {
                    $is_dividend = 1;
                }
                if (!empty($userLevel->reward)) {
                    $is_reward = 1;
                }

                DB::table('miner_user_level')
                    ->where('user_id', $user_id)
                    ->where('miner_level_id', $oldUserLevel->level)->update([
                        'miner_level_id' => $userLevel->level,
                        'is_pop_up' => $is_pop_up,
                        'is_audit' => $is_audit,
                        'is_reward' => $is_reward,
                        'is_dividend' => $is_dividend,
                        'update_time' => dateFormat()
                    ]);

                if (!empty($userLevel->reward)) {
                    $reward = json_decode($userLevel->reward, true);

                    //赠送树苗
                    $minerUserSaplingService = new MinerUserSaplingService();
                    $minerUserSaplingService->giveAway($user_id, $reward['sapling_id'], 6, '升级赠送');
                }
            }
        }
    }

    /**
     * 获取可分红的用户
     */
    public function getUserDividendLevel()
    {
        $levelList = DB::table('miner_level')
            ->where('is_delete', 0)
            ->where('is_disable', 0)
            ->orderBy('level', 'asc')
            ->select(['id', 'level', 'nickname', 'is_audit', 'rule', 'dividend_ratio', 'reward', 'is_pop_up'])
            ->get();
        $data = [];
        foreach ($levelList as $item) {
            $where = [
                ['miner_level_id', '=', $item->id],
                //['is_issue', '=', 0],
               // ['is_dividend', '=', 0],
                ['is_disable', '=', 0],
                ['is_delete', '=', 0],
                //['is_audit', '=', 0]
            ];
            $userList = DB::table('miner_user_level')->where($where)->get();
            $item->count = count($userList);
            $item->details = $userList;
            $data[] = $item;
        }
        return $data;
    }


    public function initUserLevel($userId, $levelId)
    {
        $sn = new Snowflake();
        $level = DB::table('miner_level')->where('level', $levelId)->first();
        if (empty($level)) {
            return false;
        }

        $data['id'] = $sn->nextId();
        $data['user_id'] = $userId;
        $data['miner_level_id'] = $levelId;
        $data['is_issue'] = 0;
        $data['is_disable'] = 0;
        $data['is_dividend'] = 0;
        if ($level->dividend_ratio > 0) {
            $data['is_dividend'] = 1;
        }

        $data['is_reward'] = 0;
        if (!empty($level->reward)) {
            $data['is_reward'] = 1;
        }

        $data['is_delete'] = 0;
        $data['is_pop_up'] = 1;
        if ($data['miner_level_id'] == 1) {
            $data['is_audit'] = 1;
        }
        $data['create_time'] = dateFormat();
        return DB::table('miner_user_level')->insert($data);
    }
}
