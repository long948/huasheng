<?php


namespace App\Services;


use App\Models\CoinModel;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * 商店树苗管理
 * Class MinerSaplingService
 * @package App\Services
 */
class MinerSaplingService extends Service
{

    /**
     * 树苗列表
     * @param $user_id
     * @param int $is_store
     * @return Collection
     */
    public function saplingList($user_id, $is_store = 0)
    {
        $where = [
            ['is_disable', '=', 0],
            ['is_delete', '=', 0],
        ];
        if ($is_store) {
//            $isAuth = DB::table('Members')->where('id', $user_id)->value('IsAuth');
//            if (intval($isAuth) == 1) { //实名认证后不显示赠送的小树苗
//                $where[] = ['is_experience', '=', 0];
//            }
            $where[] = ['is_experience', '=', 0];
        }
        $list = DB::table('miner_sapling')->where($where)
            ->select(['id', 'nickname', 'level', 'background_image', 'icon', 'price', 'max_hold', 'recommend_price', 'coin_id'])
            ->orderByDesc('sort')->get();

        $domain = getDomain();
        foreach ($list as $item) {
            $item->id .= '';
            $item->title = '';
            if ($item->background_image) {
                $item->background_image = $domain . $item->background_image;
            }
            if ($item->icon) {
                $item->icon = $domain . $item->icon;
            }

            $coin = CoinModel::GetById($item->coin_id);
            $item->payCoinName = $coin->Name;
            $item->payCoinLogo = $coin->Logo;
            $item->payCoinId = $coin->Id;

            //是否可购买
            $item->is_buy = 0;
            
            $userAmount = getUserAmountByCoin($user_id, $item->coin_id);
            $user_amount = $userAmount->Money;
            $item->price = $item->recommend_price;
            if ($user_amount >= $item->recommend_price) {
                $item->info = '可以种植啦!';
                $item->proportion = 1;
            } else {
                $cha = bcsub($item->recommend_price, $user_amount);
                $item->info = "还差{$cha}{$coin->Name}!";
                $item->proportion = bcdiv($user_amount, $item->recommend_price, 2);
            }
        }
        return $list;
    }

    /**
     * 获取树苗详情
     * @param $sapling_id
     * @return Builder|mixed|null
     */
    public function saplingDetail($sapling_id)
    {
        $details = DB::table('miner_sapling')->find($sapling_id);
        if ($details) {
            $userAmount = 1000;
            if ($userAmount >= $details->price) {
                $details->is_buy = 1;
                $details->info = '可以购买';
            } else {
                $details->is_buy = 0;
                $details->info = '花生米不足,请继续加油吧!';
            }
            $domain = getDomain();
            $details->background_image = $domain . $details->background_image;
            $details->icon = $domain . $details->icon;
            $details->sapling_id = $details->id . '';
            $details->cycle += basieEvent();
            $details->yield = bcdiv($details->total_profit, $details->cycle, 4);
            unset($details->id);
        }
        return $details;
    }
}
