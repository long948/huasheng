<?php


namespace App\Services\Shop;


use App\Models\CoinModel;
use App\Models\Shop\TeamActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ShopTeamActivityService
{

    /**
     * 一组活动信息
     * @param array $activityIds
     * @return Collection
     */
    public function activityDetailsByIds(array $activityIds)
    {
        return DB::table('shop_team_activity')->whereIn('activity_id', $activityIds)->get();
    }


    /**
     * 活动商品信息
     * @param array $activityIds
     * @return array
     */
    public function activityGood(array $activityIds)
    {
        $activity = $this->activityDetailsByIds($activityIds);
        if (empty($activity)) {
            return [];
        }
        $goods = [];
        $coin = CoinModel::getCoinAll();
        foreach ($activity as $item) {
            /**
             * @var $item TeamActivity
             */
            $goodService = new ShopGoodService();
            $good = $goodService->goodDetails($item->goods_id,
                ['goods_id', 'goods_name', 'market_price', 'ordinary_price', 'original_img', 'sales_sum', 'video', 'goods_state']);
            $good->proportion = 100;
            if (!$good->goods_state) {
                continue;
            }

            $payCoin = CoinModel::GetById($item->coin_id);
            $luckCoin = CoinModel::GetById($item->luck_coin_id);
            $good->isActivity = empty($activity) == null ? false : true;
            $active = [
                'activity_id' => $item->activity_id,
                'needer' => $item->needer ?? 0,
                'stock_limit' => $item->stock_limit ?? 0,
                'return_amount' => $item->return_amount ?? 0,
                'team_price' => $item->team_price ?? 0,
                'team_type' => $item->team_type,
                'store_count' => $item->store_count,
                'luck_amount' => $item->luck_amount,
                'payCoinName' => $payCoin->Name,
                'payCoinId' => $payCoin->Id,
                'luckCoinName' => $luckCoin->Name,
                'luckCoinId' => $luckCoin->Id,
                'act_name' => $item->act_name
            ];

            $active['payCoinName'] = '未知';
            foreach ($coin as $co) {
                if ($item->coin_id == $co->Id) {
                    $active['payCoinName'] = $co->Name;
                    $active['payCoinId'] = $co->Id;
                }

                if ($item->luck_coin_id == $co->Id) {
                    $active['luckCoinName'] = $co->Name;
                    $active['luckCoinId'] = $co->Id;
                }
            }

            $teamFollowService = new ShopTeamFollowService();
            $good->active = $active;
            $good->user = $teamFollowService->progressFollowPeople($item->goods_id);
            $goods[] = $good;
        }

        return $goods;
    }


    /**
     * 商品类型详情查询
     * @param $goodId
     * @param $teamType
     * @return TeamActivity
     */
    public function findActivityById($goodId, $teamType): TeamActivity
    {
        return $activity = TeamActivity::query()
            ->where('deleted', 0)
            ->where('goods_id', $goodId)
            ->where('team_type', $teamType)
            ->first();
    }


    /**
     * 增加活动商品库存
     * @param $activityId
     * @param int $num
     * @return int
     */
    public function activityIncrSalesSum($activityId, $num = 1)
    {
        TeamActivity::query()->where('activity_id', $activityId)->decrement('sales_sum', $num);
        return TeamActivity::query()->where('activity_id', $activityId)->increment('store_count', $num);
    }


    /**
     * 活动详情
     * @param $activityId
     * @return TeamActivity
     */
    public function findActivity($activityId): TeamActivity
    {
        return TeamActivity::query()->find($activityId);
    }

    /**
     * 商品比例
     * @param $activityId
     * @return bool|int|string|null
     */
    public function activityProportion($activityId)
    {
        $activity = $this->activityDetails($activityId);
        if (empty($activity)) {
            return 0;
        }
        if ($activity->virtual_num == 0) {
            return 0;
        }
        $proportion = (float)bcdiv($activity->virtual_num, ($activity->virtual_num + $activity->store_count), 2);
        return ($proportion < 0 ? 0 : $proportion) * 100;
    }


    /**
     * 活动详情
     * @param $activityId
     * @return TeamActivity
     */
    public function activityDetails($activityId): TeamActivity
    {
        return TeamActivity::query()->find($activityId);
    }

    /**
     * 根据商品编号查询活动
     * 默认为普通拼团
     * @param $goodId
     * @param $teamType
     * @return array
     */
    public function activityByGoodId($goodId, $teamType = 1)
    {

        /**
         * @var $activity TeamActivity
         */
        $activity = TeamActivity::query()
            ->where('deleted', 0)
            ->where('status', 1)
            ->where('goods_id', $goodId)
            ->where('team_type', $teamType)
            ->first();
        if (empty($activity)) {
            return [];
        }

        return [
            'activityId' => $activity->activity_id,
            'needer' => $activity->needer ?? 0,
            'stock_limit' => $activity->stock_limit ?? 0,
            'return_amount' => $activity->return_amount ?? '0.00',
            'team_price' => $activity->team_price ?? '0.00',
            'superGroupPrice' => $activity->super_group_price ?? '0.00',
            'team_type' => $activity->team_type,
            'spikeId' => '0',
            'buy_limit' => $activity->buy_limit,
            'store_count' => $activity->store_count
        ];

    }
}
