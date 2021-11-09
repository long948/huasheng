<?php


namespace App\Services\Shop;


use App\Exceptions\ArException;
use App\Models\CoinModel;
use App\Models\Shop\Good;
use App\Models\Shop\TeamActivity;
use App\Models\Shop\TeamFound;
use App\Services\Team\TeamFoundService;
use App\Services\User\UserInfoService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopGoodService
{


    /**
     * 商品详情
     * @param $goodId
     * @param string[] $field
     * @return Builder|mixed
     */
    public function goodDetails($goodId, $field = ['*'])
    {
        return DB::table('shop_good')->where('goods_id', $goodId)->select($field)->first();
    }


    /**
     * 商品详情
     * @param $goodId
     * @param $userId
     * @param $teamType
     * @param $spikeId
     * @return Good
     * @throws ArException
     */
    public function foundGoodDetails($goodId, $userId, $teamType, $spikeId)
    {
        $good = DB::table('shop_good')->where('goods_id', $goodId)->where('goods_state', '>', 0)
            ->select(['goods_id', 'goods_name',
                'market_price', 'ordinary_price',
                'keywords', 'goods_remark', 'goods_content',
                'original_img', 'sales_sum', 'video', 'cat_id', 'carousel_img', 'video',
                'fabulous', 'step_on', 'share', 'prom_type', 'videodirection', 'goods_state'])->first();
        if (empty($good)) {
            throw new ArException(ArException::SELF_ERROR, '拼购商品不存在');
        }

        //商品周边处理
        $good->carousel_img = json_decode($good->carousel_img, true);
        if (empty($good->carousel_img)) {
            $good->carousel_img = [];
        }
        $good->goods_content = html_entity_decode($good->goods_content);

        $teamActivityService = new ShopTeamActivityService();

        //活动相关参数
        $activity = $teamActivityService->findActivityById($goodId, $teamType);
        if (empty($activity)) {
            throw new ArException(ArException::SELF_ERROR, '拼购活动不存在');
        }
        if (bccomp($activity->status, '2', 2) == 0) {
            $good->goods_state = 2;
        }
        $payCoin = CoinModel::GetById($activity->coin_id);
        $luckCoin = CoinModel::GetById($activity->luck_coin_id);
        $activityInfo = [
            'needer' => $activity->needer,
            'stock_limit' => $activity->stock_limit,
            'return_amount' => $activity->return_amount ?? '0.00',
            'team_price' => $activity->team_price ?? '0.00',
            'superGroupPrice' => $activity->super_group_price ?? '0.00',
            'team_type' => $activity->team_type,
            'spikeId' => $spikeId,

            'store_count' => $activity->store_count,
            'virtual_num' => $activity->virtual_num,

            'activityId' => $activity->activity_id,
            'payCoinName' => $payCoin->Name,
            'payCoinId' => $payCoin->Id,
            'luckCoinName' => $luckCoin->Name,
            'luckCoinId' => $luckCoin->Id,
            'luck_amount' => $activity->luck_amount,
            'act_name' => $activity->act_name,

            'sales_sum' => $activity->sales_sum

        ];

        $good->sales_sum = $activity->store_count;

        $good->proportion = $teamActivityService->activityProportion($activity->activity_id);

        $teamFoundService = new ShopTeamFoundService();
        $teamFollowService = new ShopTeamFollowService();
        $userService = new UserInfoService();

        //团相关参数
        $founds = $teamFoundService->foundByActivityId($activity->activity_id);
        foreach ($founds as $k => &$found) {
            $people = $teamFollowService->getFollow($found->found_id, ['follow_user_id', 'follow_time', 'follow_id'])
                ->each(function (&$val, &$key) use ($found, $userService) {
                    $user = $userService->getInfoByUserId($val->follow_user_id, ['Id', 'NickName', 'Avatar', 'Phone']);
                    $user->userIsFound = false;
                    if (Str::is($found->user_id, $val->follow_user_id)) {
                        $user->userIsFound = true;
                    }
                    $val->user = $user;
                });
            $found->peoples = $people;

            if ($found->is_super_group) {
                unset($founds[$k]);
            }
        }
        $good->found = $founds;
        $good->active = $activityInfo;

        $peopleActivityService = new ShopPeopleActivitiesService();
        if ($teamType == 3) {

            $newPeople = $peopleActivityService->peopleActivity($spikeId);

            $timeSlotService = new ShopTimeSlotActivityService();
            if (!$timeSlotService->isActivity($spikeId, $goodId)) {
                $good->goods_state = 2;
            }

            if ($good->goods_state == 2) {
                $newPeople['isEnd'] = true;
            }
            $good->newPeople = $newPeople;
        }
        return $good;
    }


    /**
     * 商品详情
     * @param $goodId
     * @param $userId
     * @param TeamActivity $activity
     * @return Collection
     */
    public function open($goodId, $userId, TeamActivity $activity)
    {
        $open = DB::table('shop_team_found as d')
            ->join('shop_team_follow as w', 'd.found_id', 'w.found_id')
            ->where('d.user_id', $userId)
            ->where('d.found_end_time', '>', time())
            ->whereIn('d.status', [0, 1])
            ->where('w.status', 0)
            ->select(['w.follow_id as orderSn', 'd.status', 'd.found_id', 'd.team_price as amount'])
            ->first();
        if (empty($open)) {
            return null;
        }
        $open->orderSn .= '';
        $open->found_id .= '';
        $open->user = getUserAmountByCoin($userId, $activity->coin_id);
        return $open;
    }

    /**
     * 商品详情
     * @param $goodId
     * @param $userId
     * @param TeamActivity $activity
     * @return Collection
     */
    public function openIsFound($goodId, $userId, TeamActivity $activity)
    {
        $shopTeamService = new ShopTeamFoundService();
        $open = DB::table('shop_team_follow as w')
            ->where('w.follow_user_id', $userId)
            ->where('w.status', 1)
            ->where('w.is_end', 0)
            ->select(['w.follow_id as orderSn', 'w.status', 'w.found_id'])
            ->first();
        if (empty($open)) {
            return null;
        }

        $found = $shopTeamService->findFound($open->found_id);
        if (empty($found)) {
            return null;
        }

        $open->amount = $found->team_price;

        $open->orderSn .= '';
        $open->found_id .= '';
        $open->user = getUserAmountByCoin($userId, $activity->coin_id);
        return $open;
    }

    /**
     * 商品详情
     * @param $goodId
     * @param $userId
     * @param $teamType
     * @param TeamActivity $activity
     * @return Collection
     */
    public function join($goodId, $userId, TeamActivity $activity)
    {

        $join = DB::table('shop_team_follow as w')
            ->join('shop_team_found as d', 'd.found_id', 'w.found_id')
            ->where('w.good_id', $goodId)
            ->where('w.follow_user_id', $userId)
            ->where('w.is_end', 0)
            ->where('w.status', 0)
            ->select(['w.follow_id as orderSn', 'w.status', 'w.found_id', 'd.team_price as amount', 'w.follow_user_id', 'd.user_id'])
            ->first();

        if (empty($join)) {
            return null;
        }
        
        $join->orderSn .= '';
        $join->found_id .= '';
        $join->user = getUserAmountByCoin($userId, $activity->coin_id);
        if ($join->follow_user_id == $join->user_id) {
            return null;
        }

        return $join;
    }

    /**
     * 根据商品名称模糊查询
     * @param $searchGoodName
     * @return Collection
     */
    public function searchByLikeName($searchGoodName)
    {
        return Good::query()
            ->where('goods_state', 1)
            ->where('is_on_sale', 1)
            ->where('goods_name', "like", "%{$searchGoodName}%")
            ->pluck('goods_id');
    }

}
