<?php


namespace App\Services\Other;


use App\Model\Other\OtherConventional;
use App\Model\Other\UserConventional;
use App\Utils\Snowflake;

class UserConventionalService
{

    public function find($userId)
    {
        return UserConventional::query()->where('user_id', $userId)->first();
    }


    /**
     * 收益率
     * @param $userId
     * @return int|null
     */
    public function getUserLevelRateOfReturn($userId)
    {
        /**
         * @var $userLevel UserConventional
         */
        $userLevel = $this->find($userId);
        if (empty($userLevel)) {
            return 0;
        }
        return $userLevel->rate_of_return;
    }


    /**
     * 更新用户等级
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    public function changeUserLevel($userId)
    {
        /**
         * @var $newUserLevel OtherConventional
         */
        $otherConventionalService = new OtherConventionalService();
        $newUserLevel = $otherConventionalService->determination($userId);

        if (empty($newUserLevel)) {
            return false;
        }
        
        /**
         * @var $userLevel UserConventional
         */
        $userLevel = $this->find($userId);
        if (empty($userLevel)) {
            return $this->initLevel($userId, $newUserLevel->level, $newUserLevel->rate_of_return);
        }

        if ($newUserLevel->level > $userLevel->level_id) {
            $userLevel->level_id = $newUserLevel->level;
            $userLevel->rate_of_return = $newUserLevel->rate_of_return;
            return $userLevel->save();
        }
    }


    /**
     * 初始化用户等级
     * @param $userId
     * @param $levelId
     * @param $rate_of_return
     * @return bool
     * @throws \Exception
     */
    public function initLevel($userId, $levelId, $rate_of_return)
    {
        $level = new UserConventional();
        $sn = new Snowflake();
        $level->id = $sn->nextId();
        $level->user_id = $userId;
        $level->level_id = $levelId;
        $level->rate_of_return = $rate_of_return;
        return $level->save();
    }


}
