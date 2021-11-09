<?php


namespace App\Services\User;


use App\Services\MinerUserSaplingService;
use App\Utils\Snowflake;
use Illuminate\Support\Facades\DB;

/**
 * Class UserTeamService
 * @package App\Services\User
 */
class UserTeamService
{
    /**
     * 团队兑换人数 and 总的团队人数
     * exchange_people_count
     * @param $userId
     */
    public function exchange_people_count($userId)
    {
        echo "更新团队兑换人数:{$userId}\n";
        $child = userChild($userId, 6);
        $exchange_people_count = 0;
        foreach ($child as $item) {
            $count = $this->userSapling($item);
            if ($count >= 1) {
                $exchange_people_count += 1;
            }
        }

        //总的团队人数
        $total_people_count = count($child);
        DB::table('miner_user_team_info')
            ->where('user_id', $userId)
            ->update([
                'exchange_people_count' => $exchange_people_count,
                'total_people_count' => $total_people_count,
                'update_time' => dateFormat()
            ]);
    }

    /**
     * 总算力 and 总的有效算力 and 总的团队邀请算力
     * @param $userId
     */
    public function total_people_count($userId)
    {
        echo "更新总亩数|总的有效亩数|总的团队邀请亩数:{$userId}\n";
        $child = userChild($userId, 6);
        $total_exchange_computing_power = $this->teamComputingPower($child) ?? 0;

        //总的团队邀请算力
        $total_invite_computing_power = (count($child) / 10);

        //团队兑换算力 + 团队实名人数
        $total_computing_power = $total_exchange_computing_power + $total_invite_computing_power;

        DB::table('miner_user_team_info')
            ->where('user_id', $userId)
            ->update([
                'total_computing_power' => $total_computing_power,
                'total_exchange_computing_power' => $total_exchange_computing_power,
                'total_invite_computing_power' => $total_invite_computing_power,
                'update_time' => dateFormat()
            ]);
    }


    /**
     * 一直需要跑任务
     * 直推兑换算力 and 有效兑换人数
     * @param $userId
     */
    public function self_computing_power($userId)
    {
        echo "直推兑换亩数|有效兑换人数:{$userId}\n";
        $people_child = DB::table('Members')
            ->where('ParentId', $userId)
            ->where('IsAuth', 1)->pluck('Id');

        $useSaplingService = new MinerUserSaplingService();

        //直推兑换算力
        $self_computing_power = 0;
        //直推有效兑换人数
        $existing_direct_push_count = 0;
        foreach ($people_child as $item) {
            $count = $this->userSapling($item);
            if ($count >= 1) {
                $existing_direct_push_count += 1;
                $self_computing_power += $this->userComputingPower($item);
            }
        }

        //有效直推人数
        $effective_people_count = count($people_child);
        DB::table('miner_user_team_info')
            ->where('user_id', $userId)
            ->update([
                'self_computing_power' => $self_computing_power,
                'existing_direct_push_count' => $existing_direct_push_count,
                'effective_people_count' => $effective_people_count,
                'update_time' => dateFormat()
            ]);
    }

    /**
     * 奖励
     * @param $user_id
     * @throws \Exception
     */
    public function reward($user_id)
    {
        echo "正在计算奖励:{$user_id}\n";
        $team = DB::table('miner_user_team_info')->where('user_id', $user_id)->first();
        if (empty($team)) {
            return;
        }
        $reward_list = DB::table('miner_sapling_share_reward')
            ->where('is_disable', 0)
            ->where('is_delete', 0)
            ->orderBy('id')->get();
        $count = count($reward_list);


        $user_this_reward = null;
        $data['next_level'] = '您已达到最高成就,已没有奖励!';
        $sn = new Snowflake();

        for ($i = 0; $i < $count; $i++) {
            if (($i + 1) < $count) {
                $temp_power = floor($reward_list[$i + 1]->computing_power);
                $data['next_level'] = "直推需{$reward_list[$i+1]->direct_push}人兑换田,亩数总和达{$temp_power}亩";
            }

            if ($reward_list[$i]->direct_push > $team->existing_direct_push_count || $reward_list[$i]->computing_power > $team->self_computing_power) {
                $user_this_reward = $reward_list[$i]; //要获取奖励的等级
                break;
            }

            //用户是否可领取奖励
            $user_reward_record = collect(DB::table('miner_sapling_share_reward_record')
                ->where('user_id', $user_id)
                ->where('sapling_share_reward_id', $reward_list[$i]->id)
                ->first());

            //存入要奖励的数据
            if ($user_reward_record->isEmpty()) {
                if ($team->existing_direct_push_count >= $reward_list[$i]->direct_push && $team->self_computing_power >= $reward_list[$i]->computing_power) {
                    $record['id'] = $sn->nextId();
                    $record['user_id'] = $user_id;
                    $record['sapling_share_reward_id'] = $reward_list[$i]->id;
                    $record['reward'] = $reward_list[$i]->reward;
                    $record['create_time'] = dateFormat();
                    DB::table('miner_sapling_share_reward_record')->insert($record);
                }
            }
        }

        $data['info'] = "您已达到最高等级";
        $data['reward'] = "无奖励";
        $data['proportion'] = 1;

        if ($user_this_reward) {

            $temp = floor($user_this_reward->computing_power);
            $data['info'] = "直推中有{$user_this_reward->direct_push}人兑换田,亩数总和达{$temp}亩";

            $reward = json_decode($user_this_reward->reward);
            $nickname = DB::table('miner_sapling')->where('id', $reward->miner_sapling_id)->value('nickname');
            $data['reward'] = "{$nickname}*{$reward->number}";

            $proportion_count = ($team->existing_direct_push_count / $user_this_reward->direct_push);
            $proportion_count = $proportion_count > 1 ? 1 : $proportion_count;

            $proportion_power = ($team->self_computing_power / $user_this_reward->computing_power);
            $proportion_power = $proportion_power > 1 ? 1 : $proportion_power;
            $data['proportion'] = ($proportion_count + $proportion_power) / 2; //比例
        }

        $data['update_time'] = dateFormat();

        DB::table('miner_user_team_info')->where('user_id', $user_id)->update($data);
    }


    /**
     * 一组人的算力
     * @param $child
     * @return mixed
     */
    private function teamComputingPower($child)
    {
        return DB::table('miner_user_computing_power')
            ->whereIn('user_id', $child)
            ->where('type', '>', 1)
            ->sum('computing_power');
    }


    /**
     * 获取用户自身亩数
     * @param $user_id
     * @return mixed
     */
    private function userComputingPower($user_id)
    {
        return DB::table('miner_user_computing_power')
            ->where('user_id', $user_id)
            ->where('type', '>', 1)
            ->sum('computing_power');
    }

    /**
     * 用户树苗
     * @param $userId
     * @return int
     */
    private function userSapling($userId)
    {
        return DB::table('miner_user_sapling')
            ->where('user_id', $userId)
            ->where('type', 2)
            ->count('id');
    }
}
