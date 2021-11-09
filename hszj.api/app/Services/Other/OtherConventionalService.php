<?php


namespace App\Services\Other;


use App\Model\Other\OtherConventional;
use App\Model\Other\UserConventional;
use App\Services\MinerUserComputingPowerService;
use App\Utils\Snowflake;
use App\Utils\UserUtil;

class OtherConventionalService
{

    public function find($userId)
    {
        return OtherConventional::query()->where('user_id', $userId)->first();
    }

    public function list()
    {
        return OtherConventional::query()->get();
    }


    public function determination($userId)
    {
        $directPush = UserUtil::getDirectPush($userId); //直推有效人数
        $userComputingPowerService = new MinerUserComputingPowerService();
        $userComputingPower = $userComputingPowerService->userComputingPower($userId);//花生田亩数

        $list = $this->list()->sortByDesc('level');
        $newLevel = null;
        foreach ($list as $key) {
            $rule = json_decode($key->rule, true);
            if (!is_array($rule)) {
                continue;
            }

            if ($rule['direct_push'] <= count($directPush) && $rule['mu'] <= $userComputingPower) {
                $newLevel = $key;
                break;
            }
        }
        return $newLevel;
    }
}
