<?php


namespace App\Services\Shop;


use App\Models\Shop\TimeSlotActivity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ShopTimeSlotActivityService
{

    public function timeSlotActivityGood()
    {
        $result = [
            'isStart' => false,
            'isEnd' => false,
            'title' => '活动结束',
            'limit' => 0,
            'needer' => 0,
            'stock_limit' => 0,
            'beginTime' => time(),
            'endTime' => time(),
            'good' => []
        ];

        $peopleActivityService = new ShopPeopleActivitiesService();
        $activity = $peopleActivityService->peopleActivity();
        if (empty($activity)) {
            return $result;
        }

        $page = 1;
        $limit = 20;
        $activityIds = $this->getSpikeTimeSlotActivityIds($activity->id, $page, $limit);
        if ($activityIds->isEmpty()) {
            return $result;
        }


        //活动类型
        $teamType = 3;
        $teamActivityGoodService = new ShopTeamActivityService();
        $good = $teamActivityGoodService->activityGood($activityIds->toArray(), $teamType);
        foreach ($good as &$item) {
            $active = $item->active;
            $active['spikeId'] = (string)$activity->id;
            $item->active = $active;
        }
        return [
            'isStart' => $activity->begin_time < time() ? true : false,
            'isEnd' => $activity->end_time < time() ? true : false,
            'title' => $activity->title,
            'limit' => $activity->limit,
            'needer' => $activity->needer,
            'stock_limit' => $activity->stock_limit,
            'beginTime' => $activity->begin_time,
            'endTime' => $activity->end_time,
            'good' => $good
        ];
    }

    /**
     * 获取秒杀中的活动编号
     * @param $timeSlotId
     * @param $page
     * @param $count
     * @return Collection
     */
    public function getSpikeTimeSlotActivityIds($timeSlotId, $page, $count)
    {
        return DB::table('shop_time_slot_activity')->where('time_slot_id', $timeSlotId)
            ->where('is_delete', 0)
            ->pluck('team_activity_id');
    }


    /**
     * 商品是否还处于活动专区
     * @param $sessionId
     * @param $goodId
     * @return bool
     */
    public function isActivity($sessionId, $goodId)
    {
        return DB::table('shop_time_slot_activity')->where('time_slot_id', $sessionId)
            ->where('good_id', $goodId)
            ->where('is_delete', 0)
            ->exists();
    }
    
}
