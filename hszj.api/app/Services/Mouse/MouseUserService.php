<?php


namespace App\Services\Mouse;


use App\Common\DTO\UserDeductionDTO;
use App\Exceptions\ArException;
use App\Jobs\UserMouseRestJob;
use App\Models\CoinModel as Coin;
use App\Models\Dog\DogUser;
use App\Models\Mouse\MouseList;
use App\Models\Mouse\MouseUser;
use App\Services\Dog\DogUserService;
use App\Services\Service;
use App\Services\User\MinerUserSaplingTotalReleaseService;
use App\Services\User\UserTotalIncomeService;
use App\Utils\Enum\Enums;
use App\Utils\RandomUtil;
use App\Utils\RedisLock;
use App\Utils\Snowflake;
use App\Utils\UserUtil;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Psr\SimpleCache\InvalidArgumentException;

class MouseUserService extends Service
{

    /**
     * @param $userId
     * @return Collection|null
     */
    public function list($userId)
    {
        return MouseUser::query()->where('user_id', $userId)->get()->each(function (&$key, $val) {
            /**
             * @var $key MouseUser
             */
            $key->mouseType;
            $key->proportion = bcdiv($key->frequency, $key->max_frequency, 4);
        });
    }

    /**
     * @param $userId
     * @param $mouseUserId
     * @return Model|null
     */
    public function find($userId, $mouseUserId)
    {
        $mouse = MouseUser::query()->where('id', $mouseUserId)->where('user_id', $userId)->first();
        if (empty($mouse)) {
            return null;
        }
        $mouse->mouseType;
        return $mouse;
    }

    /**
     * 老鼠是否在疗伤
     * @param MouseUser $mouseUser
     * @return bool
     */
    public function isInHealing(MouseUser $mouseUser)
    {
        if ($mouseUser->is_in_healing) {
            return true;
        }
        return false;
    }

    /**
     * 是否达到最大偷取次数
     * @param MouseUser $mouseUser
     * @return bool
     */
    public function isFrequency(MouseUser $mouseUser)
    {
        if ($mouseUser->frequency > $mouseUser->max_frequency) {
            return true;
        }
        return false;
    }

    /**
     * 是否需要删除
     * @param MouseUser $mouseUser
     */
    public function deleteMouse(MouseUser $mouseUser)
    {
        if ($mouseUser->frequency >= $mouseUser->max_frequency) {
            $mouseUser->is_disable = true;
            $mouseUser->is_delete = true;
            $mouseUser->save();
        }
    }


    /**
     * 设置老鼠受伤
     * @param MouseUser $mouseUser
     * @return bool
     * @throws InvalidArgumentException
     */
    public function mouseHealing(MouseUser $mouseUser)
    {
        if (!$mouseUser->is_healing) {
            return true;
        }
        $key = env('REDIS_PREFIX') . ":mouse:rest:{$mouseUser->user_id}:{$mouseUser->id}";

        if (!$mouseUser->healing_time > 0) {
            $mouseUser->healing_time = 30; //默认30分钟
        }

        $mouseUser->is_in_healing = true;
        $mouseUser->healing_time_ltt = time() + ($mouseUser->healing_time * 60);
        if ($mouseUser->save()) {
            dispatch(new UserMouseRestJob($mouseUser->refresh()))
                ->delay(now()->addMinutes($mouseUser->healing_time))
                ->onQueue(Enums::QUEUE_NAME['mouse_healing']);
            return Cache::set($key, $mouseUser->id, (bcmul($mouseUser->healing_time, 60)));
        }
        return false;
    }

    /**
     * 老鼠受伤后恢复
     * @param MouseUser $mouseUser
     * @return bool
     * @throws InvalidArgumentException
     */
    public function restoreMouseHealing(MouseUser $mouseUser)
    {
        $mouseUser->is_in_healing = false;
        $key = env('REDIS_PREFIX') . ":mouse:rest:{$mouseUser->user_id}:{$mouseUser->id}";
        //防止时间不一致，导致缓存中的老鼠还在疗伤
        Cache::delete($key);
        return $mouseUser->save();
    }

    /**
     * 获取可偷取的用户信息
     * @param $userId
     * @return Collection
     */
    public function getStealUsers($userId)
    {
        $userChild = UserUtil::getDirectPush($userId);
        $parentId = UserUtil::getParentId($userId);
        $keyName = Enums::CACHE_KEY['user_mouse_steal'] . "{$userId}_";
        return collect($userChild)->add($parentId)->each(function (&$key, $val) use ($keyName) {
            $keyName .= $key->id;
            $key->is_setal = true; //是否可以偷取
            if (Cache::has($keyName)) {
                $key->is_setal = false;
            }
            if ($key->Avatar) {
                $key->Avatar = $this->QiniuDomain() . $key->Avatar;
            }
        });
    }

    /**
     * 偷取的前置检测
     * @param $userId
     * @param $userMouseId
     * @param $stealUserId
     * @return MouseUser|null
     * @throws ArException
     * @throws InvalidArgumentException
     */
    public function beforeStealIncome($userId, $userMouseId, $stealUserId)
    {
        $day = getDay();
        //24小时自然日后可以再次偷取
        $stealTimeLtt = $day['endTime'] - time();
        $key = Enums::CACHE_KEY['user_mouse_steal'] . "{$userId}_{$stealUserId}";
        if (Cache::has($key)) {
            throw new ArException(ArException::SELF_ERROR, '您已经偷取过他的了');
        }

        /**
         * @var $userMouse MouseUser
         */
        $userMouse = $this->find($userId, $userMouseId);
        if (empty($userMouse)) {
            throw new ArException(ArException::SELF_ERROR, '花鼠不存在,请重新购买');
        }

        if ($this->isInHealing($userMouse)) {
            throw new ArException(ArException::SELF_ERROR, '该花鼠已受伤,正在恢复中');
        }

        $userMouse->frequency += 1;
        if ($this->isFrequency($userMouse)) {
            throw new ArException(ArException::SELF_ERROR, '花鼠受伤严重,不能再偷取了');
        }

        $this->deleteMouse($userMouse);

        if (!$userMouse->save()) {
            throw new ArException(ArException::SELF_ERROR, '抱歉,您的花鼠什么也没偷到');
        }

        if (Cache::set($key, $stealUserId, $stealTimeLtt)) {
            return $userMouse;
        }
        return null;
    }

    /**
     * 偷取收益
     * @param $userId
     * @param $userMouseId
     * @param $stealUserId
     * @return mixed
     * @throws ArException
     * @throws \Throwable
     */
    public function stealIncome($userId, $userMouseId, $stealUserId)
    {
        return RedisLock::lock(Enums::REDIS_LOCK_KEY['USER_STEAL_INCOME'] . $userId, function ()
        use ($userId, $userMouseId, $stealUserId) {
            //前置检查
            $userMouse = $this->beforeStealIncome($userId, $userMouseId, $stealUserId);

            if (!$userMouse instanceof MouseUser) {
                throw new ArException(ArException::SELF_ERROR, '抱歉,您的花鼠什么也没偷到');
            }

            //不管偷没偷到 老鼠要进行休息
            if (!$this->mouseHealing($userMouse)) {
                throw new ArException(ArException::SELF_ERROR, '抱歉,您的花鼠什么也没偷到');
            }

            //此处是被偷人已经没有可偷取的金额了
            $userTotalReleaseService = new MinerUserSaplingTotalReleaseService();
            $userIsSteal = $userTotalReleaseService->getDayIsSteal($stealUserId);
            if ($userIsSteal == false) {
                throw new ArException(ArException::SELF_ERROR, '抱歉,您的花鼠什么也没偷到');
            }

            $userTotalIncomeService = new UserTotalIncomeService();

            //查看被偷取的人的仓库是否还有花生米
            $stealAmount = $userTotalIncomeService->getUserAmount($stealUserId);
            if (!$stealAmount > 0) {
                throw new ArException(ArException::SELF_ERROR, '抱歉,您的花鼠什么也没偷到');
            }

            //是否可以偷取
            $is_steal = true;

            $userDogService = new DogUserService();
            $isDog = $userDogService->isStandGuard($stealUserId);

            //偷取的比例
            $steal = RandomUtil::rangeRandom($userMouse->min_steal, $userMouse->max_steal);
            if ($isDog) {
                /**
                 * @var $userDog DogUser
                 */
                $userDog = $userDogService->getStandGuard($stealUserId);
                //计算狗的防御比例
                $defense = RandomUtil::rangeRandom($userDog->min_defense, $userDog->max_defense);
                if (bcsub($defense, $steal, 4) > 0) { //狗的战斗力比鼠要高出一些
                    $is_steal = false;
                }
            }

            if (!$is_steal) {
                throw new ArException(ArException::SELF_ERROR, '抱歉,您的花鼠什么也没偷到');
            }

            $userStealAmount = bcmul($userIsSteal, $steal, 4);
            return DB::transaction(function () use ($userMouse, $stealUserId, $userStealAmount, $userTotalReleaseService, $userId, $userTotalIncomeService) {
                //更改被偷人仓库及今日偷取量等
                $userTotalReleaseService->userSteal($stealUserId, $userStealAmount);

                $userDeductionDTO = new UserDeductionDTO();
                $userDeductionDTO->setUserId($stealUserId);
                $userDeductionDTO->setChildId($userId);
                $userDeductionDTO->setBusinessId(0);
                $userDeductionDTO->setMethod(2);
                $userDeductionDTO->setType(2);
                $userDeductionDTO->setRemarks('花生米被偷');
                $userDeductionDTO->setAmount($userStealAmount);
                $userDeductionDTO->setStatus(1);
                $userDeductionDTO->setCoinId(0);
                $userTotalIncomeService->changeUserAmount($userDeductionDTO);

                $coin = Coin::GetByEnName();
                DB::table('MemberCoin')->where('MemberId', $userId)
                    ->where('CoinId', $coin->Id)
                    ->update([
                        'Money' => DB::raw("Money+{$userDeductionDTO->getAmount()}")
                    ]);

                $this->AddLog($userId, $userDeductionDTO->getAmount(), $coin, 'steal_income');

                return $userStealAmount;
            });
        }, 5);
    }

    /**
     * 购买花鼠
     * @param $userId
     * @param $mouseId
     * @return bool
     * @throws ArException
     * @throws \Throwable
     */
    public function buy($userId, $mouseId)
    {
        return RedisLock::lock(Enums::REDIS_LOCK_KEY['USER_BUY_HOUSE'] . $userId, function () use ($userId, $mouseId) {
            $mouseService = new MouseListService();
            /**
             * @var $mouse MouseList
             */
            $mouse = $mouseService->find($mouseId);
            if (empty($mouse)) {
                throw new ArException(ArException::SELF_ERROR, '选择的花鼠现不能购买');
            }

            //需要到达对应的常规等级
            $userLevelId = getUserConventionalLevelId($userId);
            if ($userLevelId < $mouse->user_level) {
                $levelName = getConventionalLevelName($mouse->user_level) ?? '相应的';;
                throw new ArException(ArException::SELF_ERROR, "需达到{$levelName}等级才能购买此花鼠");
            }

            $userCoin = Coin::GetById($mouse->coin_id);
            $userAmount = getUserAmountByCoin($userId, $userCoin->Id);
            if ($mouse->price > $userAmount->Money) {
                throw new ArException(ArException::SELF_ERROR, '您的余额不足');
            }

            return DB::transaction(function () use ($userId, $mouse, $userCoin) {
                $is_buy = DB::table('MemberCoin')
                    ->where('MemberId', $userId)
                    ->where('CoinId', $userCoin->Id)
                    ->update([
                        'Money' => DB::raw("Money-{$mouse->price}")
                    ]);

                if ($is_buy) {
                    $this->addUserMouse($userId, $mouse);
                    self::AddLog($userId, (-$mouse->price), $userCoin, 'buy_mouse');
                }
                return true;
            });
        });
    }

    /**
     * 新增用户花鼠
     * @param $userId
     * @param MouseList $mouse
     * @return bool
     * @throws \Exception
     */
    public function addUserMouse($userId, MouseList $mouse)
    {
        $userMouse = new MouseUser();
        $sn = new Snowflake();
        $userMouse->id = $sn->nextId();
        $userMouse->user_id = $userId;
        $userMouse->mouse_list_id = $mouse->id;
        $userMouse->price = $mouse->price;

        $userMouse->is_healing = $mouse->is_healing;
        $userMouse->healing_time = $mouse->healing_time;
        $userMouse->max_hold = $mouse->max_hold;
        $userMouse->min_steal = $mouse->min_steal;
        $userMouse->max_steal = $mouse->max_steal;
        $userMouse->max_frequency = $mouse->max_frequency;
        $userMouse->explanation = $mouse->explanation;
        $userMouse->sort = $mouse->sort;
        $userMouse->is_experience = $mouse->is_experience;
        return $userMouse->save();
    }

}
