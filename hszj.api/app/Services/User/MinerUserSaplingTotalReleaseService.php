<?php


namespace App\Services\User;


use App\Common\DTO\UserDeductionDTO;
use App\Exceptions\ArException;
use App\Models\User\MinerUserSaplingTotalRelease;
use App\Utils\DateUtil;
use App\Utils\Snowflake;
use Illuminate\Support\Facades\DB;

class MinerUserSaplingTotalReleaseService
{

    /**
     * 今日总收益记录
     * @param $userId
     * @return Model|object|null
     */
    public function dayFind($userId)
    {
        $day = DateUtil::getDay();
        return MinerUserSaplingTotalRelease::query()->where('user_id', $userId)
            ->whereBetween('create_time', [$day['beginTime'], $day['endTime']])
            ->first();
    }

    /**
     * 记录被偷金额
     * @param $userId
     * @param $stealAmount
     * @return bool
     */
    public function userSteal($userId, $stealAmount)
    {
        /**
         * @var $userRelease MinerUserSaplingTotalRelease
         */
        $userRelease = $this->dayFind($userId);
        $userRelease->already_steal_amount += $stealAmount;
        if ($userRelease->already_steal_amount >= $userRelease->steal_amount) {
            $userRelease->is_steal = false;
        }
        return $userRelease->save();
    }


    /**
     * 初始化今日收益
     * @param $userId
     * @param $amount
     * @param $begin_receive_time
     * @return bool
     * @throws \Exception
     */
    public function initTotalRelease($userId, $amount, $begin_receive_time)
    {
        $release = new MinerUserSaplingTotalRelease();
        $steal_ratio = DB::table('setting')->where('k', 'steal_ratio')->value('v');
        if (!$steal_ratio > 0) {
            return false;
        }
        $sn = new Snowflake();
        $release->id = $sn->nextId();
        $release->user_id = $userId;
        $release->amount = $amount;
        $release->is_steal = 1;
        $release->steal_amount = bcmul($amount, $steal_ratio, 4);
        $release->already_steal_amount = 0;
        $release->begin_receive_time = $begin_receive_time;
        return $release->save();
    }

    /**
     * 今日是否还可被偷
     * @param $userId
     * @return bool|float|null
     */
    public function getDayIsSteal($userId)
    {
        /**
         * @var $dayIncome MinerUserSaplingTotalRelease
         */
        $dayIncome = $this->dayFind($userId);

        if (empty($dayIncome)) {
            return false;
        }

        if (!$dayIncome->is_steal) {
            return false;
        }
        if ($dayIncome->already_steal_amount >= $dayIncome->steal_amount) {
            return false;
        } else {
            return ($dayIncome->steal_amount - $dayIncome->already_steal_amount);
        }
    }


    /**
     * 累计收益
     * @param $userId
     * @return bool
     * @throws ArException
     * @throws \Throwable
     */
    public function cumulativeIncome($userId)
    {
        /**
         * @var $release MinerUserSaplingTotalRelease
         */
        $release = $this->dayFind($userId);
        if (empty($release)) {
            return false;
        }
        if (($release->is_issue)) {
            return false;
        }

        DB::transaction(function () use ($release) {

            $release->is_issue = true;
            $release->issue_time = time();
            $release->save();

            $userDeductionDTO = new UserDeductionDTO();
            $userDeductionDTO->setUserId($release->user_id);
            $userDeductionDTO->setChildId(0);
            $userDeductionDTO->setBusinessId($release->id);
            $userDeductionDTO->setMethod(1);
            $userDeductionDTO->setType(1);
            $userDeductionDTO->setRemarks('花田收益');
            $userDeductionDTO->setAmount($release->amount);
            $userDeductionDTO->setStatus(1);
            $userDeductionDTO->setCoinId(0);

            $userTotalIncomeService = new UserTotalIncomeService();
            $userTotalIncomeService->changeUserAmount($userDeductionDTO);
            echo "累计完成\n";
        });
    }

    /**
     * 获取今日花生米收益
     * @param $userId
     * @return float
     */
    public function getDayAmount($userId)
    {
        /**
         * @var $totalRelease MinerUserSaplingTotalRelease
         */
        $totalRelease = $this->dayFind($userId);
        if (empty($totalRelease)) {
            return 0;
        }
        return $totalRelease->amount;
    }


    /**
     * 获取总的收益
     * @param $userId
     * @return mixed
     */
    public function getAmount($userId)
    {
        return MinerUserSaplingTotalRelease::query()
            ->where('user_id', $userId)
            ->where('is_issue', 1)
            ->sum('amount');
    }
}
