<?php


namespace App\Services\System;


use App\Common\DTO\UserDeductionDTO;
use App\Exceptions\ArException;
use App\Models\CoinModel as Coin;
use App\Models\SettingModel;
use App\Services\Service;
use App\Services\UserGiveAwayService;
use Illuminate\Support\Facades\DB;

class SystemService extends Service
{


    /**
     * @var UserGiveAwayService
     */
    private $userGiveAwayService;

    /**
     * SystemService constructor.
     * @param UserGiveAwayService $userGiveAwayService
     */
    public function __construct(UserGiveAwayService $userGiveAwayService)
    {
        $this->userGiveAwayService = $userGiveAwayService;
    }


    /**
     *  解冻
     * @param $user_id
     * @param $type
     * @throws ArException
     */
    public function userFrozen($user_id, $type)
    {
        $amount = SettingModel::getValueByKey('unfrozen_amount');
        $IsFrozenCTC = DB::table('members')->where('id', $user_id)->value('IsFrozenCTC');
        if (intval($IsFrozenCTC) == 0) {
            throw new ArException(ArException::SELF_ERROR, '您未被冻结,无需解冻');
        }
        
        if ($type == 1) {
            $coin = Coin::GetByEnName();
            $userAmount = getUserAmountByCoin($user_id, $coin->Id);
            if ($userAmount->Money < $amount) {
                throw new ArException(ArException::SELF_ERROR, '您的余额不足');
            }
            //扣钱
            DB::table('MemberCoin')
                ->where('MemberId', $user_id)
                ->where('CoinId', $coin->Id)
                ->update([
                    'Money' => DB::raw("Money-{$amount}"),
                ]);
            //添加账单记录
            self::AddLog($user_id, (-$amount), $coin, 'unfrozen_amount');
        }

        if ($type == 2) {
            $userGiveAwayAmount = $this->userGiveAwayService->getUserAmount($user_id);
            if ($userGiveAwayAmount >= $amount) {
                $userDTO = new UserDeductionDTO();
                $userDTO->setUserId($user_id);
                $userDTO->setChildId(0);
                $userDTO->setBusinessId(0);
                $userDTO->setMethod(2);
                $userDTO->setType(2);
                $userDTO->setRemarks('解冻账号');
                $userDTO->setAmount($amount);
                $userDTO->setStatus(1);
                $userDTO->setCoinId(0);
                $result = $this->userGiveAwayService->changeUserAmount($userDTO);
            }
        }

        //解冻
        DB::table('Members')->where('Id', $user_id)->update([
            'IsFrozenCTC' => 0
        ]);

    }
}
