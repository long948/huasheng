<?php


namespace App\Services\Shop;


use App\Models\Shop\TeamLottery;
use App\Utils\RandomUtil;
use App\Utils\Snowflake;
use Illuminate\Support\Collection;

class ShopTeamLotteryServiceService
{


    /**
     * 中奖记录
     * @param $userId
     * @param $page
     * @param $count
     * @return Collection
     */
    public function lottery($userId, $page, $count)
    {
        $offset = $page <= 0 ? 1 : $page;
        $limit = $count > 20 || $count <= 0 ? 20 : $count;
        $offset = ($offset - 1) * $limit;

        $goodService = new ShopGoodService();
        $userLottery = TeamLottery::query()->where('user_id', $userId)
            ->limit($limit)
            ->offset($offset)
            ->orderBy('create_time', 'asc')
            ->get();
        foreach ($userLottery as &$item) {
            /**
             * @var $good Good
             */
            $good = $goodService->goodDetails($item->good_id);
//            /**
//             * @var $order Order
//             */
//            $order = $orderService->findOrder($item->order_sn);
//            $item->order = [
//                'shop_price' => $order->order_amount,
//                'number' => 1
//            ];
            $item->good = [
                'good_id' => $good->goods_id,
                'original_img' => $good->original_img,
                'good_name' => $good->goods_name
            ];
        }
        return $userLottery;
    }


    /**
     * @param $userId
     * @param $orderId
     * @param $orderSn
     * @param $activityId
     * @param $goodId
     * @param $foundId
     * @param $followId
     * @return bool
     * @throws \Exception
     */
    public function saveLottery($userId, $orderId, $orderSn, $activityId, $goodId, $foundId, $followId)
    {
        $teamLottery = new TeamLottery();
        $teamLottery->user_id = $userId;
        $teamLottery->good_id = $goodId;
        $teamLottery->order_id = $orderId;
        $teamLottery->order_sn = $orderSn;
        $teamLottery->activity_id = $activityId;
        $teamLottery->found_id = $foundId;
        $teamLottery->follow_id = $followId;
        return $teamLottery->save();
    }


    /**
     * 订单是否是参团订单
     * @param $orderSn
     * @return Model|object|null
     */
    public function orderIsLottery($orderSn)
    {
        return TeamLottery::query()->where('order_sn', $orderSn)->first();
    }


    /**
     * @param $userId
     * @param $followId
     * @return TeamLottery
     */
    public function lotteryByUserIdAndFoundId($userId, $followId): TeamLottery
    {
        return TeamLottery::query()->where('user_id', $userId)->where('follow_id', $followId)->first();
    }
}
