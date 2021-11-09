<?php


namespace App\Services;


use App\Exceptions\ArException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * 分享奖励
 * Class MinerUserLevelService
 * @package App\Services
 */
class MinerSaplingShareRewardService extends Service
{

    /**
     * 获取奖励规则
     * @return Collection
     */
    public function shareRewardList()
    {
        return DB::table('miner_sapling_share_reward')->where([
            ['is_disable', '=', 0],
            ['is_delete', '=', 0],
        ])->get();
    }


    /**
     * 发放分享奖励
     * @param $user_id
     * @return mixed|void
     * @throws \Throwable
     */
    public function userReward($user_id)
    {
        $reward = collect(DB::table('miner_sapling_share_reward_record')->where([
            ['user_id', '=', $user_id],
            ['is_reward', '=', 0],
        ])->orderBy('create_time', 'asc')->first());
        if ($reward->isEmpty()) {
            return false;
        }

        $rule = json_decode($reward['reward']);
        if (empty($rule->miner_sapling_id) || empty($rule->number)) {
            return false;
        }

        $minerSaplingService = new MinerSaplingService();
        $sapling = $minerSaplingService->saplingDetail($rule->miner_sapling_id);
        if (empty($sapling)) {
            return false;
        }

        return DB::transaction(function () use ($reward, $rule) {
            DB::table('miner_sapling_share_reward_record')->where('id', $reward['id'])->update([
                'is_reward' => 1,
                'reward_time' => dateFormat()
            ]);
            $userSaplingService = new MinerUserSaplingService();
            for ($i = $rule->number; $i > 0; $i--) {
                $userSaplingService->giveAway($reward['user_id'], $rule->miner_sapling_id, 4
                    , "达到{$reward['sapling_share_reward_id']}级条件分享奖励赠送,总数为：{$rule->number}块田，当前为第{$i}块");
            }
            return true;
        });
    }


}
