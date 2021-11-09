<?php


namespace App\Services\Shop;


use App\Exceptions\ArException;
use App\Jobs\UserShopOrderPay;
use App\Models\CoinModel as Coin;
use App\Models\Shop\Good;
use App\Models\Shop\TeamFollow;
use App\Models\Shop\TeamFound;
use App\Services\Service;
use App\Services\Team\TeamActivityService;
use App\Services\User\UserInfoService;
use App\Utils\RandomUtil;
use App\Utils\RedisLock;
use App\Utils\Snowflake;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Psy\Util\Str;

class ShopTeamFollowService extends Service
{


    /**
     * 团下面的用户
     * @param $foundId
     * @return Collection
     */
    public function followPeoples($foundId)
    {
        $follow = $this->getFollow($foundId, ['follow_id', 'follow_user_id', 'follow_time', 'is_end', 'is_lock_draw']);
        $userService = new UserInfoService();
        foreach ($follow as &$item) {
            $user = $userService->getInfoByUserId($item->follow_user_id, ['NickName', 'Avatar', 'Phone', 'Id']);
            $item->user_name = $user->NickName;
            $item->avatar = $user->Avatar;
            $item->phone_number = $user->Phone;
            $item->user_id = $user->Id;
        }
        return $follow;
    }


    /**
     * 正在活动下拼团的用户
     * @param $goodId
     * @return array
     */
    public function progressFollowPeople($goodId)
    {
        //筛选正在拼团等用户
        $follow = DB::table('shop_team_follow')
            ->where('team_type', '>', 1)
            ->where('good_id', $goodId)->where(function ($query) {
                return $query->where('status', 1)
                    ->where('is_end', 0);
            })->get();
        $result = [];
        $userService = new UserInfoService();
        foreach ($follow as &$item) {
            $user = $userService->getInfoByUserId($item->follow_user_id, ['Id', 'NickName', 'Avatar', 'Phone']);
            $result[] = ['user_name' => $user->NickName, 'avatar' => $user->Avatar];
        }
        return $result;
    }


    /**
     * 活动团下人
     * @param $foundId
     * @param string[] $field
     * @return Collection
     */
    public function getFollow($foundId, $field = ['*'])
    {
        return TeamFollow::query()->where('found_id', $foundId)
            ->where('status', 1)
            ->select($field)
            ->orderBy('pay_time')->get();
    }


    /**
     * 保存参团信息
     * @param $userId
     * @param $teamId
     * @param $foundId
     * @param $goodId
     * @param $userNote
     * @param $teamType
     * @param $endTime
     * @return string
     * @throws \Exception
     */
    public function saveFollow($userId, $teamId, $foundId, $goodId, $userNote, $teamType, $endTime)
    {

        $teamFollow = new TeamFollow();
//        $teamFollow->follow_id = (new Snowflake())->nextId();
        $teamFollow->follow_user_id = $userId;
        $teamFollow->follow_time = time();
        $teamFollow->found_id = $foundId;
        $teamFollow->activity_id = $teamId;
        $teamFollow->good_id = $goodId;
        $teamFollow->status = 0;
        $teamFollow->address_id = 0;
        $teamFollow->user_note = $userNote;
        $teamFollow->team_type = $teamType;
        $teamFollow->end_pay_time = $endTime;
        $teamFollow->save();
        $teamFollow->refresh();
        return $teamFollow->follow_id;
    }


    /**
     * @param $userId
     * @param $foundId
     * @return bool
     */
    public function userExistsFound($userId, $foundId)
    {
        return TeamFollow::query()
            ->where('follow_user_id', $userId)
            ->where('found_id', $foundId)
            ->whereIn('status', [0, 1])
            ->exists();
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
     * 团状态
     * @param TeamFollow $teamFollow
     * @return int
     */
    public function followStatus(TeamFollow $teamFollow)
    {
        /**
         * 拼团状态 0全部 1待支付 2待成团 3已完成 4已取消
         */
        if ($teamFollow->status == 0) {
            return 1;
        }

        if ($teamFollow->status == 1 && !$teamFollow->is_end) {
            return 2;
        }

        if ($teamFollow->status == 1 && $teamFollow->is_end) {
            return 3;
        }

        if ($teamFollow->status == 2) {
            return 4;
        }
    }


    /**
     * @param $followId
     * @return TeamFollow
     */
    public function findFollow($followId): TeamFollow
    {
        return TeamFollow::query()->find($followId);
    }


    /**
     * 我的拼团
     * @param $userId
     * @param $status
     * @param $page
     * @param $count
     * @param $searchName
     * @return Collection
     */
    public function userFollow($userId, $status, $page, $count, $searchName)
    {
        $offset = $page <= 0 ? 1 : $page;
        $limit = $count > 20 || $count <= 0 ? 20 : $count;
        $offset = ($offset - 1) * $limit;
        $goodId = collect();
        $goodService = new ShopGoodService();
        if (!empty($searchName)) {
            $goodId = $goodService->searchByLikeName($searchName);
        }

        $orderFiled = 'follow_time';
        if ($status == 2 || $status == 3) { //待成团
            $orderFiled = 'pay_time';
        }

        $teamFollow = TeamFollow::query()
            ->where('follow_user_id', $userId)
            ->where('status', '!=', 3)
            ->where(function ($query) use ($status, $goodId) {
                /**
                 * @var $query Builder
                 */
                if (intval($status) > 0) {
                    $query->where($this->followStatusWhere($status));
                }

                if ($goodId->isNotEmpty()) {
                    $query->whereIn('good_id', $goodId);
                }
                return $query;
            })->limit($limit)
            ->offset($offset)
            ->orderBy($orderFiled, 'desc')
            ->select([
                'follow_id', 'follow_time', 'found_id',
                'status', 'pay_time', 'is_lock_draw',
                'is_refund', 'refund_transaction_id',
                'refund_time', 'is_end', 'end_pay_time', 'good_id'
            ])->get();

        $teamFoundService = new ShopTeamFoundService();
        $teamActivityService = new ShopTeamActivityService();
        /**
         * @var $item TeamFollow
         */
        foreach ($teamFollow as &$item) {
            /**
             * @var $foundInfo TeamFound
             */
            $foundInfo = $teamFoundService->findFound($item->found_id);

            $item->setAttribute('orderSn', $item->follow_id);
            $item->setAttribute('orderAmount', $foundInfo->team_price);
            $item->status = $this->followStatus($item);

            $found = [
                'need' => $foundInfo->need,
                'foundStatus' => $foundInfo->status,
                'info' => '已取消',
                'returnAmount' => $foundInfo->return_amount,
                'principal' => $foundInfo->team_price,
                'team_price' => $foundInfo->team_price,
                'invitation_code' => $foundInfo->invitation_code
            ];

            if ($item->status == 1) {
                $found['info'] = '待支付';
            }

            if ($item->status == 2) {
                $people = $foundInfo->need - $foundInfo->join;
                $found['info'] = $people > 0 ? "待成团,差{$people}人" : '已成团,请等待抽奖';
            }

            if ($item->status == 3) {
                $found['info'] = '已完成';
            }

            if ($item->status == 3 && $found['foundStatus'] == 3) {
                $found['info'] = '拼团失败';
            }

            if ($item->status == 4) {
                if (empty($item->end_pay_time)) {
                    $found['info'] = '已取消,未支付';
                } else {
                }
            }

            $found['join'] = $foundInfo->join;
            $found['need'] = $foundInfo->need;
            $found['found_time'] = $foundInfo->found_time;
            $found['found_end_time'] = $foundInfo->found_end_time;

            $item->setAttribute('found', $found);

            /**
             * @var $good Good
             */
            $good = $goodService->goodDetails($item->good_id);
            $item->setAttribute('good', [
                'original_img' => $good->original_img,
                'good_name' => $good->goods_name,
                'shop_price' => $foundInfo->team_price,
                'good_id' => $good->goods_id
            ]);

            $activity = $teamActivityService->findActivity($foundInfo->activity_id);
            $payCoin = Coin::GetById($foundInfo->coin_id);
            $luckCoin = Coin::GetById($foundInfo->luck_coin_id);
            $item->setAttribute('active', [
                'needer' => $foundInfo->need,
                'stock_limit' => $foundInfo->stock_limit,
                'return_amount' => $foundInfo->return_amount,
                'team_price' => $foundInfo->team_price,
                'team_type' => $foundInfo->team_type,
                'spikeId' => $foundInfo->spike_id,
                'payCoinName' => $payCoin->Name,
                'payCoinId' => $payCoin->Id,
                'luckCoinName' => $luckCoin->Name,
                'luckCoinId' => $luckCoin->Id,
                'luck_amount' => $foundInfo->luck_amount,
                'act_name' => $activity->act_name
            ]);
            $item->setAttribute('user', getUserAmountByCoin($userId, $foundInfo->coin_id));
        }

        return $teamFollow;
    }

    /**
     * @param $status
     * @return array
     */
    public function followStatusWhere($status)
    {
        /**
         * 拼团状态 0全部 1待付款  2待成团 3已完成 4 已取消
         */
        if ($status == 1) {
            $where['status'] = 0;
            return $where;
        }

        if ($status == 2) {
            $where['status'] = 1;
            $where['is_end'] = 0;
            return $where;
        }

        if ($status == 3) {
            $where['status'] = 1;
            $where['is_end'] = 1;
            return $where;
        }

        if ($status == 4) {
            $where['status'] = 2;
            return $where;
        }

    }


    /**
     * 我的拼团详情
     * @param $userId
     * @param $followId
     * @return TeamFollow
     * @throws ArException
     */
    public function userFollowDetails($userId, $followId)
    {
        /**
         * @var $teamFollow TeamFollow
         */
        $teamFollow = TeamFollow::query()->where('follow_user_id', $userId)->where('follow_id', $followId)->first();
        if (empty($teamFollow)) {
            throw new ArException(ArException::SELF_ERROR, '拼购记录不存在');
        }

        $teamFoundService = new ShopTeamFoundService();
        /**
         * @var $found TeamFound
         */
        $found = $teamFoundService->findFound($teamFollow->found_id);
        $foundInfo = [
            'found_time' => $found->found_time,
            'found_end_time' => $found->found_end_time,
            'join' => $found->join,
            'need' => $found->need,
            'return_amount' => $found->return_amount,
            'foundStatus' => $found->status,
            'stock_limit' => $found->stock_limit,
            'info' => '未知状态',
            'open_found_time' => $found->open_found_time ?? 0
        ];

        $peoples = $this->followPeoples($found->found_id);
        foreach ($peoples as &$people) {
            $people->setAttribute('userIsFound', false);

            if (\Illuminate\Support\Str::is($found->user_id, (string)$people->user_id)) {
                $people->setAttribute('userIsFound', true);
            }
        }

        $foundInfo['peoples'] = $peoples;
        $teamFollow->status = $this->followStatus($teamFollow);
        $teamFollow->setAttribute('orderSn', $teamFollow->follow_id);

        if ($teamFollow->status == 1) {
            $foundInfo['info'] = '待支付';
        }

        if ($teamFollow->status == 2) {
            $people = $found->need - $found->join;
            $foundInfo['info'] = $people > 0 ? "待成团,差{$people}人" : '已成团,请等待抽奖';
        }

        if ($teamFollow->status == 3) {
            $foundInfo['info'] = '已完成';
        }

        if ($teamFollow->status == 4) {
            $foundInfo['info'] = '已取消';
        }

        $teamFollow->setAttribute('found', $foundInfo);
        $teamFollow->setAttribute('isFound', $found->user_id == $userId ? true : false);

        $goodService = new ShopGoodService();
        /**
         * @var $good Good
         */
        $good = $goodService->goodDetails($teamFollow->good_id);
        $teamFollow->setAttribute('good', [
            'original_img' => $good->original_img,
            'good_name' => $good->goods_name,
            'shop_price' => $good->shop_price,
            'good_id' => $good->goods_id
        ]);

        $activityService = new ShopTeamActivityService();
        $activity = $activityService->findActivity($found->activity_id);
        $payCoin = Coin::GetById($found->coin_id);
        $luckCoin = Coin::GetById($found->luck_coin_id);
        $teamFollow->setAttribute('active', [
            'needer' => $found->need,
            'stock_limit' => $found->stock_limit,
            'return_amount' => $found->return_amount,
            'team_price' => $found->team_price,
            'team_type' => $found->team_type,
            'spikeId' => $found->spike_id,
            'payCoinName' => $payCoin->Name,
            'payCoinId' => $payCoin->Id,
            'luckCoinName' => $luckCoin->Name,
            'luckCoinId' => $luckCoin->Id,
            'luck_amount' => $found->luck_amount,
            'invitation_code' => $found->invitation_code,
            'act_name' => $activity->act_name
        ]);

        $lotteryService = new ShopTeamLotteryServiceService();
        //是否中奖
        if ($teamFollow->is_lock_draw) {
            $lottery = $lotteryService->lotteryByUserIdAndFoundId($userId, $teamFollow->follow_id);
            if (!empty($lottery)) {
                $teamFollow->setAttribute('lottery', ['orderSn' => $lottery->order_sn]);
            }
        }

        $teamFollow->user = getUserAmountByCoin($userId, $found->coin_id);
        return $teamFollow;
    }


    /**
     * 我的拼团支付
     * @param $userId
     * @param $followId
     * @return string
     * @throws ArException
     */
    public function userFoundPay($userId, $followId)
    {
        /**
         * @var $teamFollow TeamFollow
         */
        $teamFollow = TeamFollow::query()
            ->where('follow_user_id', $userId)
            ->where('follow_id', $followId)->first();

        if (empty($teamFollow)) {
            throw new ArException(ArException::SHOP_USER_FOLLOW_NOT_EXISTS);
        }

        if ($teamFollow->end_pay_time <= time()) {
            throw new ArException(ArException::SHOP_FOLLOW_TIME_OUT);
        }
        if ($teamFollow->status != 0) {
            throw new ArException(ArException::SHOP_FOLLOW_PAY_END);
        }

        $teamFoundService = new ShopTeamFoundService();
        /**
         * @var $found TeamFound
         */
        $found = $teamFoundService->findFound($teamFollow->found_id);
        if (empty($found)) {
            throw new ArException(ArException::SHOP_FOUND_NOT_EXISTS);
        }
        if ($found->status != 0) {
            throw new ArException(ArException::SHOP_FOUND_END);
        }
        if ($found->found_end_time <= time()) {
            throw new ArException(ArException::SHOP_FOUND_END);
        }

        if (bccomp($found->join, $found->need) == 0) {
            throw new ArException(ArException::SHOP_FOUND_END);
        }
        
        return RedisLock::lock('userFoundPay' . $found->found_id, function () use ($userId, $teamFollow, $found) {
            DB::beginTransaction();
            try {
                $coin = Coin::GetById($found->coin_id);
                $userAmount = getUserAmountByCoin($userId, $found->coin_id);
                if ($userAmount->Money < $found->team_price) {
                    throw new ArException(ArException::SELF_ERROR, '您的余额不足');
                }

                //扣钱
                $result = DB::table('MemberCoin')->where('MemberId', $userId)
                    ->where('Money', '>=', $found->team_price)
                    ->where('CoinId', $found->coin_id)->update([
                        'Money' => DB::raw("Money-{$found->team_price}")
                    ]);
                if (!$result) {
                    DB::rollBack();
                    return false;
                }

                self::AddLog($userId, (-$found->team_price), $coin, 'shop_pay');
                $teamFoundService = new ShopTeamFoundService();
                $teamFollow->status = 1;
                $teamFollow->pay_time = time();
                if (!$teamFollow->save()) {
                    DB::rollBack();
                    return false;
                }

                $foundResult = TeamFound::query()->where('found_id', $found->found_id)
                    ->where('join', $found->join)
                    ->update(['join' => ($found->join + 1)]);

                if ($foundResult) {
                    $found->refresh();
                    if (bccomp($found->join, $found->need) == 0) {
                        $found->status = 1;
                        $found->update();
                        $teamFoundService->openFoundLuckDraw($found->found_id);
                    }
                    DB::commit();
                    return true;
                }

                DB::rollBack();
                return false;
            } catch (\Exception $e) {
                DB::rollBack();
                throw new ArException($e->getCode(), $e->getMessage());
            }
        });
    }


    /**
     * 回调修改订单状态
     * @param $followId
     * @return bool
     * @throws ArException
     */
    public function followNotify($followId): bool
    {
        $follow = $this->findFollow($followId);
        if (empty($follow)) {
            return false;
        }

        if ($follow->status != 0) {
            return false;
        }

        $teamFoundService = new ShopTeamFoundService();
        $found = $teamFoundService->findFound($follow->found_id);
        //此处说明需要退款
        if (empty($found) || $found->status != 0 || bccomp($found->join, $found->need) == 0) {
            $userInfoService = new UserInfoService();
            $follow->status = 3;
            $follow->save();
            $userInfoService->refund($follow->follow_user_id, $found->coin_id, $found->team_price);
            return false;
        }

        $follow->status = 1;
        $follow->pay_time = time();
        if ($follow->save()) {
            return $teamFoundService->joinFound($follow->found_id);
        }

        return false;
    }


    /**
     * 取消所有未支付的参团信息
     * @param $foundId
     */
    public function cancelNotPayFollow($foundId)
    {
        TeamFollow::query()->where('found_id', $foundId)->where('status', 0)->update([
            'status' => 2,
            'is_end' => 1,
            'user_note' => '团已结束,系统自动取消！'
        ]);
    }


    /**
     * 标记参与团人数已结束
     * @param $foundId
     */
    public function followSEnd($foundId)
    {
        TeamFollow::query()->where('found_id', $foundId)->update(['is_end' => 1]);
    }


    /**
     * 获取超时订单
     */
    public function getTimeOutOrder()
    {
        return TeamFollow::query()->where('status', 0)->where('end_pay_time', '<', time())->get();
    }


    /**
     * 参团支付超时取消
     * @param $userId
     * @param $followId
     * @return bool|void
     */
    public function followTimeOutCancel($userId, $followId)
    {
        $follow = $this->findFollow($followId);
        if (empty($follow)) {
            return;
        }

        $teamFoundService = new ShopTeamFoundService();
        $found = $teamFoundService->findFound($follow->found_id);

        if ($follow->status == 1) {
            return;
        }

        Db::transaction(function () use ($follow, $userId, $found) {
            $teamActivityService = new ShopTeamActivityService();
            /**
             * 只有是团长未支付的时候才会加回库存
             * 且关闭团状态
             */
            if ($found->user_id == $userId) {
                $teamActivityService->activityIncrSalesSum($found->activity_id, $found->stock_limit);
                $found->status = 3;
                $found->is_end = true;
                $found->save();
            }
            $follow->status = 2;
            $follow->is_end = true;
            return $follow->save();
        });
    }


    /**
     * 活动团下人
     * @param $foundId
     * @param string[] $field
     * @return Collection
     */
    public function getFoundUser($foundId, $field = ['*'])
    {
        return TeamFollow::query()->where('found_id', $foundId)
            ->where('status', 1)
            ->select($field)->get();
    }

}
