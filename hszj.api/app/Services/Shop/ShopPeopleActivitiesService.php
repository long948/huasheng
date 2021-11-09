<?php


namespace App\Services\Shop;


use App\Services\Service;
use Illuminate\Support\Facades\DB;

class ShopPeopleActivitiesService extends Service
{


    public function peopleActivity()
    {
        return DB::table('shop_people_activities')->where('is_delete', 0)
            ->where(function ($query) {
                return $query->where('begin_time', '<=', time())->where('end_time', '>=', time());
            })->orderBy('end_time', 'desc')->first();
    }


    public function spikeTimeSlot($spikeId)
    {
        $activities = DB::table('shop_people_activities')->where('is_delete', 0)->where('id', $spikeId)->first();
        $result = [
            'isStart' => false,
            'isEnd' => false
        ];
        if (empty($activities)) {
            return $result;
        }
        if ($activities->begin_time <= time() && $activities->end_time > time()) {
            $result['isStart'] = true;
            $result['isEnd'] = false;
        }

        return $result;
    }
}
