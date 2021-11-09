<?php


namespace App\Services;


use App\Models\CoinModel;
use App\Utils\Snowflake;
use Illuminate\Support\Facades\DB;

/**
 * 分红管理
 * Class MinerDividendService
 * @package App\Services
 */
class MinerDividendService extends Service
{

    /**
     * 获取最新的一条分红
     */
    public function getFirstDividend()
    {
        return DB::table('miner_dividend')
            ->where('is_dividend', 0)
            ->where('begin_dividend_time', '<', dateFormat())
            ->orderBy('begin_dividend_time', 'desc')->first();
    }

    /**
     * 分红奖励树苗
     * @param $user_id
     * @return mixed|void
     * @throws \Throwable
     */
    public function rewardUserSapling($user_id)
    {
        $level = collect(DB::table('miner_user_level')->where('user_id', $user_id)
            ->where('is_reward', 0)
            ->where('is_issue', 0)
            ->orderBy('create_time', 'desc')->first());
        if ($level->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($level) {
            //赠送树苗
            $give = collect(DB::table('miner_level')->where('level', $level['miner_level_id'])->first());

            if (empty($give)) {
                return;
            }
            $reward = json_decode($give['reward'], true);

            $userSaplingService = new MinerUserSaplingService();
            $d = $userSaplingService->giveAway($level['user_id'], $reward['sapling_id'], 3, "等级《{$give['nickname']}》发放的奖励");
            if ($d) {
                DB::table('miner_user_level')->where('id', $level['id'])->update([
                    'is_reward' => 1,
                    'reward_time' => dateFormat()
                ]);
            }
        });
    }

    /**
     * 分红定时任务（每周一次）
     * @throws \Throwable
     */
    public function userDividend()
    {
        $dividend = $this->getFirstDividend();

        if (empty($dividend)) {
            return false;
        }
        $minerUserLevelService = new MinerUserLevelService();
        $userDividendList = $minerUserLevelService->getUserDividendLevel();
        $level_dividend_json = [];
        $sn = new Snowflake();
        $rule = json_decode($dividend->rule, true);
        foreach ($userDividendList as $item) {

            //等级所分到的总额
            $levelAmount = bcmul($rule["miner_level_{$item->level}"], $dividend->amount, 4);

            $userLevelAmount = 0;
            $userLevelCount = $item->count;
            if ($userLevelCount > 0) {
                //单个用户所分到的
                $userLevelAmount = bcdiv($levelAmount, $item->count, 4);
            }

            //记录下每个等级所分到金额及相关信息
            $level_dividend_json[] = [
                'miner_level_' . $item->id => $item->id,
                'miner_level_amount' => $levelAmount,
                'miner_level_count' => $userLevelCount,
                'miner_level_user_amount' => $userLevelAmount
            ];
            if ($userLevelAmount < 1) {
                continue;
            }
            //具体到每个人身上
            foreach ($item->details as $detail) {
                DB::transaction(function () use ($detail, $item, $dividend, $userLevelAmount, $sn) {
                    //修改余额
                    $coin = CoinModel::GetByEnName();
                    $userAmount = getUserAmountByCoin($detail->user_id, $coin->Id);
                    DB::table('MemberCoin')->where('MemberId', $detail->user_id)->where('CoinId', $coin->Id)->update([
                        'Money' => DB::raw("Money+{$userLevelAmount}")
                    ]);

                    self::AddLog($detail->user_id, $userLevelAmount, $coin, 'dividend');

                    //记录分红记录
                    $data['id'] = $sn->nextId();
                    $data['user_id'] = $detail->user_id;
                    $data['miner_dividend_id'] = $dividend->id;
                    $data['user_level'] = $detail->miner_level_id;
                    $data['amount'] = $userLevelAmount;
                    $data['create_time'] = dateFormat();
                    DB::table('miner_user_dividend')->insert($data);

                    //标识用户已分红
                    $detailUpdate = [];
//                    if ($detail->is_reward) {
//                        $detailUpdate['is_issue'] = 1;
//                    }
                    //$detailUpdate['is_dividend'] = 1;
                    $detailUpdate['dividend_time'] = dateFormat();
                    $detailUpdate['update_time'] = dateFormat();
                    DB::table('miner_user_level')->where('id', $detail->id)->update($detailUpdate);
                });
            }
        }
        DB::table('miner_dividend')->where('id', $dividend->id)->update([
            'level_dividend_json' => json_encode($level_dividend_json),
            'is_dividend' => 1,
            'update_time' => dateFormat()
        ]);
        DB::table('setting')->where('k', 'dividend_ctc_tx_fee')->increment('v', $dividend->amount);
    }
}
