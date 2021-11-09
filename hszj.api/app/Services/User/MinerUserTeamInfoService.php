<?php


namespace App\Services\User;


use App\Jobs\UserTeamQueue;
use Illuminate\Support\Facades\DB;

/**
 * 我的团队信息计算
 * Class MinerUserTeamInfoService
 * @package App\Services
 */
class MinerUserTeamInfoService
{

    /**
     *    团队兑换算力：按照实际的树苗算力计算（total_computing_power）
     *    直推兑换算力：按照实际的树苗算力计算（self_computing_power）
     *    有效兑换人数：该数据有状态，购买树苗时加1（existing_direct_push_count）
     *    团队兑换人数：该数据有状态，购买树苗时加1（exchange_people_count）
     * @param $userId
     * @param $totalPower
     */
    public function exchangePeopleCount($userId, $totalPower)
    {
        $parents = userParents($userId, 6);
        
        if ($parents) {
            DB::table('miner_user_team_info')->whereIn('user_id', $parents)->update([
                //团队兑换算力
                'total_computing_power' => DB::raw("total_computing_power + {$totalPower}"),
                //总的有效算力
                'total_exchange_computing_power' => DB::raw("total_exchange_computing_power + {$totalPower}")
            ]);
        }

        //直推兑换算力
        $ParentId = DB::table('Members')->where('id', $userId)->value('ParentId');
        if (!empty($ParentId)) {
            DB::table('miner_user_team_info')->where('user_id', $ParentId)->update([
                'self_computing_power' => DB::raw("self_computing_power + {$totalPower}")
            ]);
        }

        return DB::transaction(function () use ($userId, $parents, $ParentId) {

            //有效兑换人数
            $userTeamInfoExtendService = new UserTeamInfoExtendService();
            $userTeamExtend = $userTeamInfoExtendService->find($userId);

            if (!$userTeamExtend->is_existing_direct_push_count) {
                DB::table('miner_user_team_info')->where('user_id', $ParentId)->update([
                    'existing_direct_push_count' => DB::raw("existing_direct_push_count + 1"),
                    'update_time' => dateFormat()
                ]);
                DB::table('miner_user_team_info_extend')->where('user_id', $userId)->update(['is_existing_direct_push_count' => 1]);
            }

            if (!$userTeamExtend->is_exchange_people_count) {
                DB::table('miner_user_team_info')->whereIn('user_id', $parents)->update([
                    'exchange_people_count' => DB::raw("exchange_people_count + 1")
                ]);
                DB::table('miner_user_team_info_extend')->where('user_id', $userId)->update(['is_exchange_people_count' => 1]);
            }

            foreach ($parents as $parent) {
                dispatch(new UserTeamQueue($parent));
            }
        });
    }
}
