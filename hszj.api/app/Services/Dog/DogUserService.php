<?php


namespace App\Services\Dog;


use App\Exceptions\ArException;
use App\Jobs\UserDogRestJob;
use App\Jobs\UserDogStandGuardJob;
use App\Models\CoinModel;
use App\Models\CoinModel as Coin;
use App\Models\Dog\DogList;
use App\Models\Dog\DogUser;
use App\Services\Service;
use App\Utils\Enum\Enums;
use App\Utils\RedisLock;
use App\Utils\Snowflake;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Psr\SimpleCache\InvalidArgumentException;

class DogUserService extends Service
{

    /**
     * @param $userId
     * @return Collection
     */
    public function list($userId)
    {
        return DogUser::query()->where('user_id', $userId)->get()->each(function (&$key, $val) {
            /**
             * @var $key DogUser
             */
            $key->dogType;
            $key->proportion = bcdiv($key->defense_count, $key->max_defense_count, 4);
        });
    }

    /**
     * @param $userId
     * @param $dogUserId
     * @return Model|null
     */
    public function find($userId, $dogUserId)
    {
        $dog = DogUser::query()->where('user_id', $userId)->where('id', $dogUserId)->first();
        $dog->dogType;
        return $dog;
    }


    /**
     * 股权购买
     * @param $userId
     * @param $id
     * @param $number
     * @return mixed
     * @throws ArException
     * @throws \Throwable
     */
    public function equityDividend($userId, $id, $number = 1)
    {

        throw new ArException(ArException::SELF_ERROR, '购买通道暂时关闭,请等待开启～');
        $dogListService = new DogListService();

        /**
         * @var $dog DogList
         */
        $dog = $dogListService->find($id);
        if (empty($dog)) {
            throw new ArException(ArException::SELF_ERROR, '选择的商品不存在');
        }
        $total = bcmul($dog->price, $number, 4);
        $userCoin = CoinModel::GetById($dog->coin_id);
        $userAmount = getUserAmountByCoin($userId, $userCoin->Id);
        if (bccomp($total, $userAmount->Money, 4) == 1) {
            throw new ArException(ArException::SELF_ERROR, '您的余额不足');
        }

        return DB::transaction(function () use ($userId, $dog, $userCoin, $total, $number) {
            $is_buy = DB::table('MemberCoin')
                ->where('MemberId', $userId)
                ->where('CoinId', $userCoin->Id)
                ->update(['Money' => DB::raw("Money-{$total}")]);
            if ($is_buy) {

                DB::table('other_equity_dividend')->insert([
                    'user_id' => $userId,
                    'number' => $number,
                    'price' => $total,
                    'create_time' => time()
                ]);

                self::AddLog($userId, (-$total), $userCoin, 'equityDividend');
            }
            return true;
        });
    }

    /**
     * 购买大黄
     * @param $userId
     * @param $dogId
     * @return bool
     * @throws ArException
     * @throws \Throwable
     */
    public function buy($userId, $dogId)
    {
        return RedisLock::lock(Enums::REDIS_LOCK_KEY['USER_BUY_DOG'] . $userId, function () use ($userId, $dogId) {
            $dogListService = new DogListService();
            /**
             * @var $dog DogList
             */
            $dog = $dogListService->find($dogId);
            if (empty($dog)) {
                throw new ArException(ArException::SELF_ERROR, '选择的大黄不存在');
            }

            //需要到达对应的常规等级
            $userLevelId = getUserConventionalLevelId($userId);
            if ($userLevelId < $dog->user_level) {
                $levelName = getConventionalLevelName($dog->user_level) ?? '相应的';
                throw new ArException(ArException::SELF_ERROR, "需达到{$levelName}等级才能购买此大黄");
            }

            $userCoin = Coin::GetById($dog->coin_id);
            $userAmount = getUserAmountByCoin($userId, $userCoin->Id);
            if ($dog->price > $userAmount->Money) {
                throw new ArException(ArException::SELF_ERROR, '您的余额不足');
            }

            return DB::transaction(function () use ($userId, $dog, $userCoin) {
                $is_buy = DB::table('MemberCoin')
                    ->where('MemberId', $userId)
                    ->where('CoinId', $userCoin->Id)
                    ->update(['Money' => DB::raw("Money-{$dog->price}")]);
                if ($is_buy) {
                    $this->addUserDog($userId, $dog);
                    self::AddLog($userId, (-$dog->price), $userCoin, 'buy_dog');
                }
                return true;
            });
        });
    }

    /**
     * 新增用户大黄
     * @param $userId
     * @param DogList $dog
     * @return bool
     * @throws \Exception
     */
    public function addUserDog($userId, DogList $dog)
    {
        $userDog = new DogUser();
        $sn = new Snowflake();
        $userDog->id = $sn->nextId();
        $userDog->user_id = $userId;
        $userDog->dog_list_id = $dog->id;
        $userDog->price = $dog->price;
        $userDog->min_defense = $dog->min_defense;
        $userDog->max_defense = $dog->max_defense;
        $userDog->max_defense_count = $dog->max_defense_count;
        $userDog->is_defense_interval = $dog->is_defense_interval;
        $userDog->defense_interval_time = $dog->defense_interval_time;
        $userDog->stand_guard_time_ltt = $dog->stand_guard_time_ltt;
        $userDog->max_hold = $dog->max_hold;
        $userDog->explanation = $dog->explanation;
        $userDog->explanation = $dog->explanation;
        $userDog->sort = $dog->sort;
        $userDog->is_experience = $dog->is_experience;
        return $userDog->save();
    }

    /**
     * 防御后大黄的休息
     * @param $userId
     * @param $userDogId
     * @return bool
     * @throws InvalidArgumentException
     */
    public function setDogRest($userId, $userDogId)
    {
        /**
         * @var $userDog DogUser
         */
        $userDog = $this->find($userId, $userDogId);
        if ($userDog->is_defense_interval) {
            if (!$userDog->defense_interval_time > 0) {
                $userDog->defense_interval_time = 30; //默认30分钟
            }
            $userDog->is_defense = true;
            $userDog->defense_time_ltt = strtotime("+ {$userDog->defense_interval_time} minute");
            if ($userDog->save()) {

                $key = Enums::CACHE_KEY['USER_DOG_REST'] . $userDog->user_id . ':' . $userDog->id;
                Cache::set($key, $userDog, bcmul($userDog->defense_interval_time, 60));

                dispatch(new UserDogRestJob($userDog->refresh()))
                    ->delay(now()->addMinutes($userDog->defense_interval_time))
                    ->onQueue(Enums::QUEUE_NAME['dog_rest']);
            }
        }
        return true;
    }

    /**
     * 大黄是否在休息
     * @param DogUser $dogUser
     * @return bool
     */
    public function getDogRest(DogUser $dogUser)
    {
        $key = Enums::CACHE_KEY['USER_DOG_REST'] . $dogUser->user_id . ':' . $dogUser->id;
        if (Cache::has($key)) {
            return true;
        }

        if ($dogUser->is_defense) {
            if ($dogUser->defense_time_ltt > time()) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检测大黄是否失效
     * @param DogUser $dogUser
     */
    public function testing(DogUser $dogUser)
    {
        if ($dogUser->defense_count >= $dogUser->max_defense_count) {
            $this->failureDog($dogUser);
        }
    }

    /**
     * 设置失效大黄
     * @param DogUser $dogUser
     * @return bool
     */
    public function failureDog(DogUser $dogUser)
    {
        $dogUser->is_delete = true;
        $dogUser->is_disable = 0;
        $dogUser->delete_time = time();
        return $dogUser->save();
    }

    /**
     * 修改大黄休息状态
     * @param DogUser $userDog
     * @return bool
     * @throws InvalidArgumentException
     */
    public function finishTheRest(DogUser $userDog)
    {
        /**
         * @var $userDog DogUser
         */
        $userDog->is_defense = false;
        $key = Enums::CACHE_KEY['USER_DOG_REST'] . $userDog->user_id . ':' . $userDog->id;
        //防止时间不一致，导致缓存中的大黄还在休息
        Cache::delete($key);
        return $userDog->save();
    }

    /**
     * 是否有大黄在站岗
     * @param $userId
     * @return bool
     */
    public function isStandGuard($userId)
    {
        $key = Enums::CACHE_KEY['USER_DOG_STAND_GUARD_UP'] . $userId;
        if (Cache::has($key)) {
            return true;
        }

//        return DogUser::query()
//            ->where('user_id', $userId)
//            ->where('is_defense', 0)
//            ->where('is_stand_guard', 1)->exists();
    }

    /**
     * 设置大黄站岗
     * @param $userId
     * @param $userDogId
     * @return void
     * @throws \Throwable
     */
    public function standGuardUp($userId, $userDogId)
    {
        /**
         * @var $dogUser DogUser
         */
        $dogUser = $this->find($userId, $userDogId);
        if (empty($dogUser)) {
            throw new ArException(ArException::SELF_ERROR, '选择大黄不存在');
        }

        if ($this->getDogRest($dogUser)) {
            throw new ArException(ArException::SELF_ERROR, '该大黄正在休息');
        }

        $key = Enums::CACHE_KEY['USER_DOG_STAND_GUARD_UP'] . $dogUser->user_id;
        if (Cache::has($key)) {
            throw new ArException(ArException::SELF_ERROR, '已有站岗的大黄');
        }

        return DB::transaction(function () use ($dogUser, $key) {

            DogUser::query()->where('user_id', $dogUser->user_id)
                ->where('is_stand_guard', 1)
                ->update([
                    'is_stand_guard' => 0
                ]);

            $dogUser->is_stand_guard = true;
            $dogUser->defense_count += 1;
            $dogUser->save();

            //记录大黄还有多久下岗
            $dogUser->stand_guard_time = time() + bcmul($dogUser->stand_guard_time_ltt, 60);

            Cache::set($key, $dogUser, bcmul($dogUser->stand_guard_time_ltt, 60));

            dispatch(new UserDogStandGuardJob($dogUser->refresh()))
                ->delay(now()->addMinutes($dogUser->stand_guard_time_ltt))
                ->onQueue(Enums::QUEUE_NAME['dog_stand_guard']);
        });
    }

    /**
     * 设置大黄下岗后休息
     * @param DogUser $dogUser
     * @return void
     * @throws \Throwable
     * @throws InvalidArgumentException
     */
    public function standGuardDown(DogUser $dogUser)
    {
        $dogUser->is_stand_guard = false;
        if ($dogUser->save()) {
            $this->setDogRest($dogUser->user_id, $dogUser->id);
        }
    }

    /**
     * 获取正在站岗的大黄
     * @param $userId
     * @return mixed
     */
    public function getStandGuard($userId)
    {
        $key = Enums::CACHE_KEY['USER_DOG_STAND_GUARD_UP'] . $userId;
        return Cache::get($key);
    }
}
