<?php


namespace App\Services\User;


use App\Exceptions\ArException;
use App\Models\CoinModel as Coin;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class UserInfoService extends Service
{

    public function getInfoByUserId($userId, $filed = ['Avatar', 'NickName'])
    {
        $userInfo = DB::table('Members')->where('Id', $userId)->select($filed)->first();
        if ($userInfo->Avatar) {
            $userInfo->Avatar = $this->QiniuDomain() . $userInfo->Avatar;
        }
        return $userInfo;
    }


    /**
     * 退款
     * @param $userId
     * @param $coinId
     * @param $amount
     * @throws ArException
     */
    public function refund($userId, $coinId, $amount)
    {
        $coin = Coin::GetById($coinId);
        getUserAmountByCoin($userId, $coinId);
        //加钱
        $result = DB::table('MemberCoin')->where('MemberId', $userId)->where('CoinId', $coinId)->update([
            'Money' => DB::raw("Money+{$amount}")
        ]);

        if ($result) {
            self::AddLog($userId, ($amount), $coin, 'shop_refund');
        }
    }

    /**
     * 中奖
     * @param $userId
     * @param $coinId
     * @param $amount
     * @throws ArException
     */
    public function lottery($userId, $coinId, $amount)
    {
        $coin = Coin::GetById($coinId);
        getUserAmountByCoin($userId, $coinId);
        //加钱
        $result = DB::table('MemberCoin')->where('MemberId', $userId)->where('CoinId', $coinId)->update([
            'Money' => DB::raw("Money+{$amount}")
        ]);

        if ($result) {
            self::AddLog($userId, ($amount), $coin, 'shop_lottery');
        }
    }
}
