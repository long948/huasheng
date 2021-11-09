<?php


namespace App\Services;


use App\Common\DTO\UserDeductionDTO;
use App\Http\Util\Snowflake;
use Illuminate\Support\Facades\DB;

/**
 * 赠送账户服务
 * Class UserGiveAwayService
 * @package App\Services
 */
class UserGiveAwayService
{

    /**
     * 用户总收益信息
     * @param $userId
     * @return UserTotalIncome
     */
    public function find($userId)
    {
        return DB::table('user_give_away')->where('user_id', $userId)->first();
    }

    /**
     * 获取用户总收益
     * @param $userId
     * @return float
     * @throws \Exception
     */
    public function getUserAmount($userId): float
    {
        $userTotalIncome = $this->find($userId);
        if (empty($userTotalIncome)) {
            $this->initUserTotalIncome($userId);
        }
        if ($userTotalIncome) {
            return $userTotalIncome->amount;
        }
        return 0;
    }


    /**
     * 初始化总收益
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    public function initUserTotalIncome($userId)
    {
        $sn = new Snowflake();
        return DB::table('user_give_away')->insert([
            'id' => $sn->nextId(),
            'user_id' => $userId,
            'amount' => 0,
            'before_amount' => 0,
            'after_amount' => 0,
            'create_time' => time()
        ]);
    }

    /**
     * 余额变动
     * @param UserDeductionDTO $amountDTO
     * @return bool
     * @throws \Throwable
     */
    public function changeUserAmount(UserDeductionDTO $amountDTO)
    {
        $userAmount = $this->find($amountDTO->getUserId());
        if (empty($userAmount)) {
            return false;
        }
            $userAmount->before_amount = $userAmount->amount;
            $userAmount->amount += $amountDTO->getAmount();
            $userAmount->after_amount += $amountDTO->getAmount();
//
        if ($amountDTO->getMethod() == 2) { //出账
            if (($userAmount->amount <=> $amountDTO->getAmount()) < 0) {
                return false;
            }
            $userAmount->before_amount = $userAmount->amount;
            $userAmount->amount -= $amountDTO->getAmount();
            $userAmount->after_amount -= $amountDTO->getAmount();
        }
        return DB::transaction(function () use ($userAmount, $amountDTO) {
            $result = DB::table('user_give_away')->where('user_id', $amountDTO->getUserId())->update([
                'before_amount' => $userAmount->before_amount,
                'amount' => $userAmount->amount,
                'after_amount' => $userAmount->after_amount,
                'update_time' => time()
            ]);
            if ($result) {
                return $this->addIncome($amountDTO);
            }
            return false;
        });
    }


    /**
     * 新增总收益账单
     * @param UserDeductionDTO $userDTO
     * @return bool
     * @throws \Exception
     */
    public function addIncome(UserDeductionDTO $userDTO)
    {
        $sn = new Snowflake();
        return DB::table('user_give_away_bill')->insert([
            'id' => $sn->nextId(),
            'user_id' => $userDTO->getUserId(),
            'coin_id' => $userDTO->getCoinId(),
            'user_amount_id' => 0,
            'business_id' => $userDTO->getBusinessId(),
            'user_child_id' => $userDTO->getChildId() ? $userDTO->getChildId() : 0,
            'type' => $userDTO->getType(),
            'method' => $userDTO->getMethod(),
            'amount' => $userDTO->getAmount(),
            'status' => $userDTO->getStatus(),
            'remarks' => $userDTO->getRemarks(),
            'create_time' => time()
        ]);
    }


    /**
     * 赠送账户记录
     * @param $userId
     * @return Collection
     */
    public function list($userId)
    {
        $type = [
            '邀请赠送',
            '购买树苗'
        ];
        $list = DB::table('user_give_away_bill')
            ->where('user_id', $userId)
            ->select(['id', 'type', 'method', 'amount', 'create_time', 'remarks'])
            ->orderBy('create_time', 'desc')
            ->get();
        foreach ($list as $item) {
            $item->id = $item->id . '';
            $item->type = $type[($item->type - 1)];
            $item->method = $item->method == 1 ? '进账' : '出账';
            $item->create_time = $this->dateFormat(($item->create_time));
        }
        return $list;
    }
    function dateFormat($time = '', $format = 'Y-m-d H:i:s')
    {
        if (empty($time)) {
            return date($format, time());
        }
        return date($format, $time);
    }
}
