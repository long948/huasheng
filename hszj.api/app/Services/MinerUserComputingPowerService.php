<?php


namespace App\Services;


use App\Utils\Snowflake;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * 算力管理
 * Class MinerSaplingPackageService
 * @package App\Services
 */
class MinerUserComputingPowerService extends Service
{

    /**
     * 新增算力
     * @param $user_id 用户编号
     * @param $amount 算力
     * @param $business_id 第三方业务编号
     * @param $type 类型
     * @param $is_self 是否是自己的
     * @param $begin
     * @param $end
     * @param string $remarks 备注
     * @param int $child_user_id 贡献人编号
     * @return string 主键
     * @throws \Exception
     */
    public function add($user_id, $amount, $business_id, $type, $is_self, $begin, $end, $remarks = '', $child_user_id = 0): string
    {
        $sn = new Snowflake();
        $data['id'] = $sn->nextId();
        $data['computing_power'] = $amount;
        $data['user_id'] = $user_id;
        $data['business_id'] = $business_id;
        $data['type'] = $type;
        $data['is_self'] = $is_self;
        $data['remarks'] = $remarks;
        $data['child_user_id'] = $child_user_id;
        $data['create_time'] = dateFormat();
        $data['begin_time'] = $begin;
        $data['end_time'] = $end;
        $result = DB::table('miner_user_computing_power')->insert($data);
        if ($result) {
            return $data['id'];
        }
        return '';
    }

    /**
     * 获取用户自身亩数
     * @param $user_id
     * @return mixed
     */
    public function userComputingPower($user_id)
    {
        return DB::table('miner_user_sapling as g')->join('miner_user_computing_power as p', 'g.id', 'p.business_id')
            ->where('g.user_id', $user_id)
            ->where('g.type', '>', 1)
            ->where('g.is_delete', '!=', 1)
            ->where('g.freed', '>=', 1)
            ->sum('p.computing_power');
    }

    /**
     * 获取团队兑换算力
     * @param $user_id
     * @return mixed
     */
    public function teamComputingPower($user_id)
    {
        //此处调用用户6代内的用户编号
        $child = userChild($user_id, 6);
        if (empty($child)) {
            return 0;
        }
        return DB::table('miner_user_computing_power')
            ->whereIn('user_id', $child)
            ->where('type', '>', 1)
            ->where('end_time', '>=', dateFormat())
            ->sum('computing_power');
    }

    /**
     * 获取团队邀请算力
     * @param $user_id
     * @return int|mixed
     */
    public function userTeamInviteComputingPower($user_id)
    {
        //此处调用用户6代内的用户编号
        $child = userChild($user_id, 6);
        if (empty($child)) {
            return 0;
        }
        return DB::table('Members')
            ->whereIn('Id', $child)
            ->sum('invite_computing_power');
    }

    /**
     * 实名认证后新增1T算力
     * @param $user_id
     * @return bool|int
     * @throws \Exception
     */
    public function inviteComputingPower($user_id)
    {
        $parents = userParents($user_id, 6);
        if ($parents) {
            $sn = new Snowflake();
            $data = [];
            foreach ($parents as $parent) {
                $data[] = [
                    'id' => $sn->nextId(),
                    'user_id' => $parent,
                    'computing_power' => 1,
                    'type' => 1,
                    'is_self' => 0,
                    'create_time' => dateFormat(),
                    'remarks' => '认证赠送',
                    'begin_time' => dateFormat(),
                    'end_time' => dateFormat()
                ];
            }
            DB::table('miner_user_computing_power')->insert($data);
            return DB::table('Members')->whereIn('id', $parents)->increment('invite_computing_power');
        }
        return false;
    }

    /**
     * 信息
     * @param $user_id
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function mySelfTeamInfo($user_id)
    {
        $team = DB::table('miner_user_team_info')->where('user_id', $user_id)->first();
        $saplingShareRewardService = new MinerSaplingShareRewardService();
        $saplingShareRewardService->userReward($user_id);
        if (!empty($team)) {
            return $team;
        } else {
            $this->mySelfTeamInfoStatistics($user_id);
            $team = DB::table('miner_user_team_info')->where('user_id', $user_id)->first();
            return $team;
        }
    }

    /**
     * 信息
     * @param $user_id
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function mySelfTeamInfoStatistics($user_id)
    {
        echo "正在统计编号为:{$user_id}用户的团队信息...\n";
        //每次访问我的团队时 处理还未赠送的列表
        $saplingShareRewardService = new MinerSaplingShareRewardService();
        $useSaplingService = new MinerUserSaplingService();
        $saplingShareRewardService->userReward($user_id);

        //6代内团队兑换人数
        $child = userChild($user_id, 6);
        $exchange_people_count = 0;
        foreach ($child as $item) {
            $userSaplingTemp = collect($useSaplingService->userSaplingList($item, 1));
            if ($userSaplingTemp->count() >= 1) {
                $exchange_people_count += 1;
            }
        }

        //团队兑换人数
        $data['exchange_people_count'] = $exchange_people_count;

        //团队总人数
        $data['total_people_count'] = count($child);

        //个人团队算力
        $teamComputingPower = $this->teamComputingPower($user_id) ?? 0;
        //团队兑换算力 + 团队实名人数
        $data['total_computing_power'] = $teamComputingPower + (count($child) / 10);

        //团队兑换算力
        $data['total_exchange_computing_power'] = $teamComputingPower;
        //团队邀请算力
        $data['total_invite_computing_power'] = (count($child) / 10);

        $people_child = DB::table('Members')
            ->where('ParentId', $user_id)
            ->where('IsAuth', 1)->pluck('Id');

        //直推兑换算力
        $self_computing_power = 0;
        //直推有效兑换人数
        $self_effective_people_count = 0;
        foreach ($people_child as $item) {
            $userSaplingTemp = collect($useSaplingService->userSaplingList($item, true));
            if ($userSaplingTemp->count() >= 1) {
                $self_effective_people_count += 1;
                $self_computing_power += $this->userComputingPower($item);
            }
        }

        //直推兑换算力
        $data['self_computing_power'] = $self_computing_power;

        //有效直推(实名过后就算)
        $data['effective_people_count'] = count($people_child);
        //已达到直推兑换人数
        $data['existing_direct_push_count'] = $self_effective_people_count;

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
                //$data['next_level'] = "直推需{$reward_list[$i+1]->direct_push}人兑换树,算力总和达{$temp_power}T";
                $data['next_level'] = "直推中有{$reward_list[$i+1]->direct_push}人兑换田总亩数达到{$temp_power}";
            }

            //用户是否可领取奖励
            $user_reward_record = collect(DB::table('miner_sapling_share_reward_record')
                ->where('user_id', $user_id)
                ->where('sapling_share_reward_id', $reward_list[$i]->id)
                ->first());

            if ($reward_list[$i]->direct_push > $self_effective_people_count
                || $reward_list[$i]->computing_power > $data['self_computing_power']) {
                $user_this_reward = $reward_list[$i]; //要获取奖励的等级
                break;
            }

            //存入要奖励的数据
            if ($user_reward_record->isEmpty()) {
                if ($data['effective_people_count'] >= $reward_list[$i]->direct_push
                    && $data['self_computing_power'] >= $reward_list[$i]->computing_power) {
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
            $data['info'] = "直推中有{$user_this_reward->direct_push}人兑换田总亩数达到{$temp}";
            $data['total_direct_push_count'] = $user_this_reward->direct_push;
            $reward = json_decode($user_this_reward->reward);
            $nickname = DB::table('miner_sapling')->where('id', $reward->miner_sapling_id)->value('nickname');
            $data['reward'] = "{$nickname}*{$reward->number}";

            $proportion_count = ($self_effective_people_count / $data['total_direct_push_count']);
            $proportion_count = $proportion_count > 1 ? 1 : $proportion_count;
            $proportion_power = ($self_computing_power / $user_this_reward->computing_power);
            $proportion_power = $proportion_power > 1 ? 1 : $proportion_power;
            $data['proportion'] = ($proportion_count + $proportion_power) / 2; //比例
        }

        $userTeam = DB::table('miner_user_team_info')->where('user_id', $user_id)->first();
        if (!empty($userTeam)) {
            DB::table('miner_user_team_info')->where('user_id', $user_id)->update([
                'exchange_people_count' => $data['exchange_people_count'],
                'total_people_count' => $data['total_people_count'],
                'total_computing_power' => $data['total_computing_power'],
                'total_exchange_computing_power' => $data['total_exchange_computing_power'],
                'total_invite_computing_power' => $data['total_invite_computing_power'],
                'self_computing_power' => $data['self_computing_power'],
                'effective_people_count' => $data['effective_people_count'],
                'existing_direct_push_count' => $data['existing_direct_push_count'],
                'next_level' => $data['next_level'],
                'info' => $data['info'],
                'reward' => $data['reward'],
                'proportion' => $data['proportion'],
                'total_direct_push_count' => $data['total_direct_push_count'],
                'update_time' => dateFormat()
            ]);
        } else {
            $sn = new Snowflake();
            DB::table('miner_user_team_info')->insert([
                'id' => $sn->nextId(),
                'user_id' => $user_id,
                'exchange_people_count' => $data['exchange_people_count'],
                'total_people_count' => $data['total_people_count'],
                'total_computing_power' => $data['total_computing_power'],
                'total_exchange_computing_power' => $data['total_exchange_computing_power'],
                'total_invite_computing_power' => $data['total_invite_computing_power'],
                'self_computing_power' => $data['self_computing_power'],
                'effective_people_count' => $data['effective_people_count'],
                'existing_direct_push_count' => $data['existing_direct_push_count'],
                'next_level' => $data['next_level'],
                'info' => $data['info'],
                'reward' => $data['reward'],
                'proportion' => $data['proportion'],
                'total_direct_push_count' => $data['total_direct_push_count'],
                'create_time' => dateFormat()
            ]);
        }
    }


    /**
     * 用户算力列表
     * @param $user_id
     * @return Collection
     */
    public function userComputingPowerList($user_id)
    {
        $list = collect(DB::table('miner_user_computing_power')->where([
            ['user_id', '=', $user_id],
            ['end_time', '>', dateFormat()],
        ])->select(['id', 'user_id', 'computing_power', 'type', 'create_time', 'end_time'])->get());
        $type = [
            '1' => '分享认证赠送',
            '2' => '购买花田亩数',
            '3' => '系统分享赠送',
            '4' => '系统收益赠送'
        ];
        foreach ($list as $item) {
            //是否失效 1失效
            $item->is_failure = 0;
            if ($item->type > 1) {
                $item->is_failure = $item->end_time > dateFormat() ? 0 : 1;
            }
            $item->type = $type[$item->type];
            unset($item->end_time);
            $item->computing_power = floor($item->computing_power);
        }
        return $list;
    }
}
