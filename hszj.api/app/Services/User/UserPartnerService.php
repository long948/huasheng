<?php


namespace App\Services\User;


use App\Exceptions\ArException;
use App\Models\CoinModel as Coin;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class UserPartnerService extends Service
{


    /**
     * 测试金额
     * @param $userId
     * @param $amount
     * @throws ArException
     */
    public function partner($userId,$amount)
    {
        $userCoin = Coin::GetByEnName();
        $userAmount = getUserAmountByCoin($userId, $userCoin->Id);
        $is_buy = DB::table('MemberCoin')
            ->where('MemberId', $userId)
            ->where('CoinId', $userCoin->Id)
            ->update([
                'Money' => DB::raw("Money+{$amount}")
            ]);
        if ($is_buy){
            self::AddLog($userId, ($amount), $userCoin, 'user_partner');
        }
    }
}
