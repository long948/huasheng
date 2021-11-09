<?php


namespace App\Services\Shop;


use App\Exceptions\ArException;
use App\Http\Controllers\Shop\ShopRewardService;
use App\Models\CoinModel;
use App\Models\SettingModel;
use App\Models\Shop\Good;
use App\Models\Shop\TeamActivity;
use App\Models\Shop\TeamFollow;
use App\Models\Shop\TeamFound;
use App\Services\User\UserInfoService;
use App\Services\User\UserService;
use App\Utils\Enum\ConfigEnum;
use App\Utils\RandomUtil;
use App\Utils\RedisLock;
use App\Utils\Snowflake;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ShopTeamFoundService
{

    /**
     * 专区开团
     * @param $userId
     * @param $goodId
     * @param $userNote
     * @param $activityId
     * @param $spikeId
     * @param $teamType
     * @return mixed|null
     * @throws ArException|Throwable
     */
    public function specialOpenActivity($userId, $goodId, $userNote, $activityId, $spikeId, $teamType)
    {
        return RedisLock::lock('specialOpenActivity' . $userId, function () use ($userId, $goodId, $userNote, $activityId, $spikeId, $teamType) {
            $teamActivityService = new ShopTeamActivityService();
            $activity = $teamActivityService->findActivity($activityId);
            if (empty($activity)) {
                throw new ArException(ArException::SHOP_FOUND_NOT_EXISTS);
            }
            $goodService = new ShopGoodService();
            $good = $goodService->goodDetails($activity->goods_id, ['goods_id', 'shop_price', 'virtual_limit', 'prom_type']);
            if (empty($good)) {
                throw new ArException(ArException::SHOP_ACTIVITY_GOOD_NOT_EXISTS);
            }

            $userFound = $this->findUserFound($userId, $activityId);
            if (!empty($userFound)) {
                throw new ArException(ArException::SHOP_USER_EXISTS_FOUND);
            }

            $endTime = time();
            $timeSlotService = new ShopTimeSlotActivityService();
            if (!$timeSlotService->isActivity($spikeId, $goodId)) {
                throw new ArException(ArException::SHOP_ACTIVITY_GOOD_NOT_EXISTS);
            }

            //秒杀
            if (bccomp($teamType, '2') == 0) {
                $peopleActivityService = new ShopPeopleActivitiesService();
                $peopleActivity = $peopleActivityService->peopleActivity();
                $endTime = $peopleActivity->end_time;
            }

            $delayOpenFoundTime = $endTime - time();
            $delayOrderOutTime = $endTime - time();

            $active_time_out = SettingModel::getValueByKey('activity_time_out') ?? ConfigEnum::ACTIVE_TIME_OUT;

            //开团时间
            if ($delayOpenFoundTime > $active_time_out) {
                $delayOpenFoundTime = $active_time_out;
            }

            $activity_order_time_out = SettingModel::getValueByKey('activity_order_time_out') ?? ConfigEnum::ACTIVITY_ORDER_TIME_OUT;
            //订单超时时间
            if ($delayOrderOutTime > $activity_order_time_out) {
                $delayOrderOutTime = $activity_order_time_out;
            }

            return Db::transaction(function () use ($userId, $activity, $spikeId, $delayOpenFoundTime, $delayOrderOutTime, $userNote) {
                if ($found = $this->saveActivity($userId, $activity, $spikeId, ($delayOpenFoundTime + time()))) {

                    $teamFollowService = new ShopTeamFollowService();
                    $orderSn = $teamFollowService->saveFollow($userId,
                        $activity->activity_id,
                        $found->found_id,
                        $activity->goods_id,
                        $userNote,
                        $activity->team_type,
                        (time() + $delayOrderOutTime)
                    );

                    $activity->store_count -= $activity->stock_limit;
                    $activity->sales_sum += $activity->stock_limit;
                    $activity->save();

                    $result['amount'] = $activity->team_price;
                    $result['orderSn'] = $orderSn;
                    $result['user'] = getUserAmountByCoin($userId, $activity->coin_id);
                    return $result;
                }
            });
        });
    }


    /**
     * 参团
     * @param $userId
     * @param $foundId
     * @param $userNote
     * @return mixed|void
     * @throws Exception|Throwable
     */
    public function ginsengActivity($userId, $foundId, $userNote)
    {
        return RedisLock::lock('ginsengActivity' . $foundId, function () use ($userId, $foundId, $userNote) {
            /**
             * @var $found TeamFound
             */
            $found = $this->findFound($foundId);
            if (empty($found)) {
                throw new ArException(ArException::SHOP_FOUND_NOT_EXISTS);
            }
            if ($found->join == 0) {
                throw new ArException(ArException::SHOP_FOUND_NOT_EXISTS);
            }

            //团截止了不能支付。
            if (($found->found_end_time) <= time()) {
                throw new ArException(ArException::SHOP_FOUND_END);
            }

            if (bcadd(intval($found->join), 1) > intval($found->need)) {
                throw new ArException(ArException::SHOP_FOUND_END);
            }

            if ($found->is_end) {
                throw new ArException(ArException::SHOP_FOUND_END);
            }

            $teamActivityService = new ShopTeamActivityService();
            $activity = $teamActivityService->findActivityById($found->good_id, $found->team_type);
            if (empty($activity)) {
                throw new ArException(ArException::SHOP_ACTIVITY_FOUND_END);
            }
            if ($activity->status == 2) {
                throw new ArException(ArException::SHOP_ACTIVITY_GOOD_NOT_EXISTS);
            }

            $teamFollowService = new ShopTeamFollowService();
            $userExistsFound = $teamFollowService->userExistsFound($userId, $foundId);
            if ($userExistsFound) {
                throw new ArException(ArException::SHOP_USER_EXISTS_FOUND);
            }

            $goodService = new ShopGoodService();
            $good = $goodService->goodDetails($found->good_id, ['goods_id', 'shop_price', 'virtual_limit', 'prom_type']);
            if (empty($good)) {
                throw new ArException(ArException::SHOP_ACTIVITY_GOOD_NOT_EXISTS);
            }

            //参团和开团人是一个
            if ($found->user_id == $userId) {
                throw new ArException(ArException::SHOP_JOIN_SELF_FOUNd);
            }

            $endTime = time();
            $timeSlotService = new ShopTimeSlotActivityService();
            if (!$timeSlotService->isActivity($found->spike_id, $found->good_id)) {
                throw new ArException(ArException::SHOP_ACTIVITY_END);
            }

            //秒杀
            if ($found->team_type == 2) {
                $peopleActivityService = new ShopPeopleActivitiesService();
                $peopleActivity = $peopleActivityService->peopleActivity();
                $endTime = $peopleActivity->end_time;
            }

            $delayOrderOutTime = $endTime - time();

            $activity_order_time_out = SettingModel::getValueByKey('activity_order_time_out') ?? ConfigEnum::ACTIVITY_ORDER_TIME_OUT;
            //订单超时时间
            if ($delayOrderOutTime > $activity_order_time_out) {
                $delayOrderOutTime = $activity_order_time_out;
            }

            return Db::transaction(function () use ($found, $userId, $userNote, $delayOrderOutTime) {
                $teamFollowService = new ShopTeamFollowService();
                $orderSn = $teamFollowService->saveFollow($userId,
                    $found->activity_id,
                    $found->found_id,
                    $found->good_id,
                    $userNote,
                    $found->team_type,
                    ($delayOrderOutTime + time())
                );

                $result['amount'] = $found->team_price;
                $result['orderSn'] = $orderSn;
                $result['user'] = getUserAmountByCoin($userId, $found->coin_id);

                return $result;
            });
        });
    }


    /**
     * 支付后查看团进度
     * @param $followId
     * @return void
     */
    public function foundPyNotify($followId)
    {
        $teamFollowService = new ShopTeamFollowService();
        if (!$teamFollowService->isFollow($followId)) {
            return;
        }

        $follow = $teamFollowService->findFollow($followId);
        if (empty($follow)) {
            return;
        }

        $found = $this->findFound($follow->found_id);
        $teamFollowService = new ShopTeamFollowService();
        $status = $teamFollowService->followStatus($follow);
        $result['followId'] = $follow->follow_id;
        $result['need'] = $found->need;
        $result['join'] = $found->join;
        $result['stockLimit'] = $found->stock_limit;
        $result['returnAmount'] = $found->return_amount;
        $result['status'] = $status;
        return $result;
    }


    /**
     * 根据活动编号查询团
     * @param $activityId
     * @return Collection
     */
    public function foundByActivityId($activityId)
    {
        return TeamFound::query()
            ->where('activity_id', $activityId)
            ->where('is_end', 0)
            ->where('status', 0)
            ->where('join', '>=', 1)
            ->orderBy('join', 'desc')
            ->select(['found_id', 'user_id', 'found_end_time', 'join', 'need', 'is_super_group', 'invitation_code'])
            ->get()->each(function (&$val) {
                $val->difference = $val->need - $val->join;
                unset($val->need);
                unset($val->join);
            });
    }


    /**
     * @param $userId
     * @param $activityId
     * @return Model|object|null
     */
    public function findUserFound($userId, $activityId)
    {
        return TeamFound::query()->where('user_id', $userId)
            ->where('activity_id', $activityId)
            ->where('is_end', 0)
            ->first();
    }


    /**
     * 保存开团信息
     * @param $userId
     * @param TeamActivity $activity
     * @param $spikeId
     * @param $endTime
     * @return TeamFound
     * @throws Exception
     */
    public function saveActivity($userId, TeamActivity $activity, $spikeId, $endTime)
    {

        $teamFound = new TeamFound();
        $teamFound->user_id = $userId;
        $teamFound->activity_id = $activity->activity_id;
        $teamFound->found_time = time();
        $teamFound->team_type = $activity->team_type;
        $teamFound->join = 0;
        $teamFound->stock_limit = $activity->stock_limit;
        $teamFound->need = $activity->needer;
        $teamFound->team_price = $activity->team_price;
        $teamFound->good_id = $activity->goods_id;
        $teamFound->return_amount = $activity->return_amount;
        $teamFound->status = 0;
        $teamFound->sub_commission = $activity->sub_commission;
        $teamFound->spike_id = $spikeId ?? 0;
        $teamFound->found_end_time = $endTime;
        $teamFound->coin_id = $activity->coin_id;
        $teamFound->luck_coin_id = $activity->luck_coin_id;
        $teamFound->luck_amount = $activity->luck_amount;
        //团口令
        $teamFound->invitation_code = Str::random(12);
        if ($teamFound->save()) {
            $teamFound->refresh();
            return $teamFound;
        }
        return null;
    }


    /**
     * @param $foundId
     * @return TeamFound|Builder|Model|object|null
     */
    public function findFound($foundId)
    {
        return TeamFound::query()->where('found_id', $foundId)->first();
    }


    /**
     * 添加团人数
     * @param $foundId
     * @return bool|int|void
     */
    public function joinFound($foundId)
    {
        $found = $this->findFound($foundId);
        if (empty($found)) {
            return false;
        }

        $found->join += 1;
        if (bccomp($found->join, $found->need) == 0) {
            $found->status = 1;
            $found->save();
            $this->openFoundLuckDraw($foundId);
        }

        return $found->save();
    }


    /**
     * 开团抽奖
     * @param $foundId
     * @return int
     */
    public function openFoundLuckDraw($foundId)
    {
        try {
            return Db::transaction(function () use ($foundId) {
                $found = $this->findFound($foundId);
                if (empty($found)) {
                    return "未找到团编号:{$foundId}";
                }

                if ($found->is_end) {
                    return "当前团已经结束";
                }

                $teamFollowService = new ShopTeamFollowService();
                $teamFollowService->cancelNotPayFollow($foundId);
                $teamFollowService->followSEnd($found->found_id);
                $userFollow = $teamFollowService->getFollow($foundId);

                $found->open_found_time = time();
                $found->is_end = true;

                if ($userFollow->isEmpty()) {

                    $found->status = 3;
                    $found->save();

                    //开团失败 资金原路退回
                    $teamActivityService = new ShopTeamActivityService();
                    foreach ($userFollow as $item) {
                        if ($item->status == 1) {
                            if (Str::is($found->user_id, $item->follow_user_id)) {
                                //加回库存
                                $teamActivityService->activityIncrSalesSum($found->activity_id, $found->stock_limit);
                            }
                            //退款
                            $userService = new UserInfoService();
                            $userService->refund($item->follow_user_id, $found->coin_id, $found->team_price);
                        }
                    }
                    return '开团人数不足，已结束';
                }

                $stockLimit = $found->stock_limit;

                if ($found->join < $found->need) {
                    if ($found->join < $stockLimit) {
                        $stockLimit = $found->join;
                    }
                }

                $lockDrawUser = $userFollow->random($stockLimit);

                $found->status = 2;
                $found->is_luck_draw = true;
                $found->save();
                $lockUsers = [];

                $teamLotteryService = new ShopTeamLotteryServiceService();
                $userService = new UserInfoService();
                $shopRewardService = new ShopRewardService();

                /**
                 * @var $item TeamFollow
                 */
                foreach ($lockDrawUser as $item) {

                    //保存中奖信息
                    $item->is_lock_draw = true;
                    $item->save();

                    $teamLotteryService->saveLottery($item->follow_user_id,
                        $item->follow_id,
                        $item->follow_id,
                        $found->activity_id,
                        $found->good_id,
                        $found->found_id,
                        $item->follow_id
                    );
                    $lockUsers[] = $item->follow_user_id;
                    //直接加资产
                    $userService->lottery($item->follow_user_id, $found->luck_coin_id, $found->luck_amount);

                    //推荐奖励
                    $shopRewardService->reward($item->follow_user_id, $found->team_price, $found->coin_id);

                }

                $userService = new UserInfoService();
                /**
                 * 其他人 本金 +奖励的红包退回
                 * 投递至队列中进行处理
                 */
                foreach ($userFollow as $item) {
                    //没中奖的人退换预设值
                    if (!in_array($item->follow_user_id, $lockUsers)) {
                        $userService->refund($item->follow_user_id, $found->coin_id, ($found->return_amount + $found->team_price));
                    }
                }
            });
        } catch (Exception $e) {
            return '开团编号:' . $foundId . '错误，具体原因：' . $e->getMessage();
        }
    }


    /**
     * @param $followId
     * @return bool
     */
    public function isFollow($followId): bool
    {
        return TeamFollow::query()->where('follow_id', $followId)->exists();
    }


    /**
     * 获取待开团待团信息
     */
    public function getTimeOutOrder()
    {
        return TeamFound::query()->where('found_end_time', '<', time())->where('is_end', '=', 0)->get();
    }


    /**
     * 邀请好友
     * @param $foundId
     * @return TeamFound
     * @throws ArException
     */
    public function userFoundCollages($foundId)
    {
        /**
         * @var $found TeamFound
         */
        $found = $this->findFound($foundId);
        if (empty($found)) {
            throw new ArException(ArException::SELF_ERROR, '团信息不存在');
        }

        $userService = new UserInfoService();
        $goodService = new ShopGoodService();
        $teamFollowService = new ShopTeamFollowService();

        $peoples = [];
        $teamFollowService->getFoundUser($foundId)->each(function (&$val, &$key)
        use ($userService, &$peoples, &$found) {
            $user = $userService->getInfoByUserId($val->follow_user_id, ['Id', 'NickName', 'Avatar', 'Phone']);
            $peoples[] = [
                'userIsFound' => $user->Id != $found->user_id ? false : true,
                'user_name' => $user->NickName,
                'avatar' => $user->Avatar,
                'phone_number' => substr_replace($user->Phone, '***', 3, 4),
                'follow_time' => $val->follow_time
            ];

        });


        /**
         * @var $good Good
         */
        $good = $goodService->goodDetails($found->good_id);
        $found->setAttribute('good', [
            'good_name' => $good->goods_name,
            'goods_id' => $good->goods_id,
            'shop_price' => $good->shop_price,
            'market_price' => $good->market_price,
            'original_img' => $good->original_img
        ]);
        $teamFoundService = new ShopTeamFoundService();
        $founds = $teamFoundService->foundByActivityIdLimit($found->activity_id);
        $foundData = [];
        foreach ($founds as &$item) {
            if (Str::is($found->found_id, $item->found_id)) {
                continue;
            }

            $people = $teamFollowService->getFollow($item->found_id, ['follow_user_id', 'follow_time', 'follow_id'])
                ->each(function (&$val, &$key) use ($userService, $item) {
                    $user = $userService->getInfoByUserId($val->follow_user_id, ['Id', 'NickName', 'Avatar', 'Phone']);
                    $val->setAttribute('user', [
                        'userIsFound' => $user->Id != $item->user_id ? false : true,
                        'user_name' => $user->NickName,
                        'avatar' => $user->Avatar,
                        'phone' => substr_replace($user->Phone, '***', 3, 4)
                    ]);
                });
            $item->setAttribute('peoples', $people);
            $foundData[] = $item;
        }

        $teamActivityService = new ShopTeamActivityService();
        $found->setAttribute('founds', $foundData);
        $found->setAttribute('info', '好友拼单/人齐开奖/人不满退款本金/不退不换');
        $active = $teamActivityService->activityByGoodId($found->good_id, $found->team_type);
        if (key_exists('team_type', $active)) {
            if ($active['team_type'] == 2) {
                $slotService = new ShopPeopleActivitiesService();
                $slot = $slotService->spikeTimeSlot($found->spike_id);
                $found->setAttribute('spike', ['isStart' => $slot['isStart'], 'isEnd' => $slot['isEnd']]);
            }
        }

        $active['spikeId'] = $found->spike_id;
        if ($found->is_super_group) {
            $active['team_price'] = $active['superGroupPrice'];
            $found->setAttribute('founds', []);
        }

        $found->setAttribute('active', $active);

        $payCoin = CoinModel::GetById($found->coin_id);
        $found->payCoinName = $payCoin->EnName;

        $luckCoin = CoinModel::GetById($found->luck_coin_id);
        $found->luckCoinName = $luckCoin->EnName;

        $found->peoples = $peoples;
        $found->setAttribute('goods_id', $found->good_id);
        unset($found->good_id);

        unset($found->sub_commission);
        unset($found->is_end);
        unset($found->is_luck_draw);
        unset($found->activity_id);
        return $found;
    }

    /**
     * 根据活动编号查询团
     * @param $activityId
     * @param int $limit
     * @return Collection
     */
    public function foundByActivityIdLimit($activityId, $limit = 1)
    {
        return TeamFound::query()
            ->where('activity_id', $activityId)
            ->where('is_end', 0)
            ->where('status', 0)
            ->where('join', '>=', 1)
            ->orderBy('join', 'desc')
            ->limit($limit)
            ->select(['found_id', 'user_id', 'found_end_time', 'join', 'need'])
            ->orderByDesc('join')
            ->get()->each(function (&$val, &$key) {
                $val->difference = $val->need - $val->join;
                unset($val->need);
                unset($val->join);
            });
    }


    /**
     * 根据口令获取团信息
     * @param $code
     * @return Builder|Model|object|null
     */
    public function getFoundByInvitationCode($code)
    {
        if (empty($code)) {
            return null;
        }
        return TeamFound::query()
            ->where('invitation_code', $code)
            ->where('status', '!=', 3)
            ->where('join', '>=', 1)
            ->value('found_id');
    }

}
