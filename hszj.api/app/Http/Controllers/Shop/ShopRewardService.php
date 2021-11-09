<?php


namespace App\Http\Controllers\Shop;


use App\Exceptions\ArException;
use App\Models\CoinModel as Coin;
use App\Models\SettingModel;
use App\Services\Service;
use App\Utils\UserUtil;
use Illuminate\Support\Facades\DB;

class ShopRewardService extends Service
{

    /**
     * @param $userId
     * @param $amount
     * @param $coinId
     * @throws ArException
     */
    public function reward($userId, $amount, $coinId)
    {
        $shopRewardRule = json_decode(SettingModel::getValueByKey('shop_reward_rule'), true);
        if (!is_array($shopRewardRule)) {
            return;
        }
        $parentId = UserUtil::getRoots($userId);
        for ($index = 0; $index < count($shopRewardRule); $index++) {
            if (empty($parentId[$index])) {
                continue;
            }
            $coin = Coin::GetById($coinId);
            if (!isset($shopRewardRule[($index + 1)])) {
                continue;
            }
            
            //加钱
            $rewardAmount = bcmul($amount, $shopRewardRule[($index + 1)], 2);
            $result = DB::table('MemberCoin')->where('MemberId', $parentId[$index])->where('CoinId', $coinId)->update([
                'Money' => DB::raw("Money+{$rewardAmount}")
            ]);

            if ($result) {
                self::AddLog($parentId[$index], ($rewardAmount), $coin, 'shop_reward');
            }
        }
    }

}
