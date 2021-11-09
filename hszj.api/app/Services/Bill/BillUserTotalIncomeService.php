<?php


namespace App\Services\Bill;


use App\Common\DTO\UserDeductionDTO;
use App\Models\Bill\BillUserTotalIncome;
use App\Models\Model;
use App\Utils\Snowflake;

class BillUserTotalIncomeService
{

    /**
     * 获取收益记录
     * @param $userId
     * @param $page
     * @param $count
     * @param int $beginTime
     * @param int $endTime
     * @return Builder[]|Collection
     */
    public function list($userId, $page, $count, $beginTime = 0, $endTime = 0)
    {
        $offset = $page <= 0 ? 1 : $page;
        $limit = $count > 20 || $count <= 0 ? 20 : $count;
        $offset = ($offset - 1) * $limit;

        $leaderList = BillUserTotalIncome::query()->where(function ($query) use ($beginTime, $endTime) {
            if ($beginTime && $endTime) {
                return $query->whereBetween('create_time', [$beginTime, $endTime]);
            }
        })->where('user_id', $userId)->select(['amount', 'method', 'create_time', 'type'])
            ->offset($offset)
            ->limit($limit)
            ->get();
        //var_dump($leaderList[0]->getOriginal('method'));
        return $leaderList;
    }


    /**
     * 详情
     * @param $leadershipId
     * @return Model
     */
    public function find($leadershipId)
    {
        return BillUserTotalIncome::find($leadershipId);
    }


    /**
     * 新增总收益账单
     * @param UserDeductionDTO $userDeductionDTO
     * @return bool
     * @throws \Exception
     */
    public function addIncome(UserDeductionDTO $userDeductionDTO)
    {
        $sn = new Snowflake();
        $userLeadership = new BillUserTotalIncome();

        $userLeadership->id = $sn->nextId();
        $userLeadership->user_id = $userDeductionDTO->getUserId();
        $userLeadership->coin_id = $userDeductionDTO->getCoinId();
        $userLeadership->user_amount_id = 0;
        $userLeadership->business_id = $userDeductionDTO->getBusinessId();
        $userLeadership->user_child_id = $userDeductionDTO->getChildId() ? $userDeductionDTO->getChildId() : 0;
        $userLeadership->type = $userDeductionDTO->getType();
        $userLeadership->method = $userDeductionDTO->getMethod();
        $userLeadership->amount = $userDeductionDTO->getAmount();
        $userLeadership->status = $userDeductionDTO->getStatus();
        $userLeadership->remarks = $userDeductionDTO->getRemarks();
        return $userLeadership->save();
    }
}
