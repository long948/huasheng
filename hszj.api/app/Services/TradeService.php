<?php


namespace App\Services;

use App\Exceptions\ArException;
use Illuminate\Support\Facades\DB;
use App\Models\CoinModel as Coin;
use App\Models\MembersModel as Members;
use App\Models\MemberCoinModel as MemberCoin;

class TradeService extends Service
{

    /**
     * @method 取消订单
     */
    public function Cancle(int $uid, $oid){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if(empty($oid)) throw new ArException(ArException::PARAM_ERROR);
        $trade = DB::table('Trade')
            ->where('MemberId', $uid)
            ->where('State', 1)
            ->where('OrderNumber', $oid)
            ->where('Type', 1)
            ->first();
        if(empty($trade)) throw new ArException(ArException::SELF_ERROR,'您没有此订单');

        DB::table('Trade')->where('Id', $trade->Id)->update([
            'State' => 4,
            'FinishTime' => time()
        ]);
    }

    /**
     * @method 确认支付
     */
    public function Pay(int $uid, $oid){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if(empty($oid)) throw new ArException(ArException::PARAM_ERROR);

        $trade = DB::table('Trade')
            ->where('MemberId', $uid)
            ->where('State', 1)
            ->where('OrderNumber', $oid)
            ->where('Type', 1)
            ->first();
        if(empty($trade)) throw new ArException(ArException::SELF_ERROR,'您没有此订单');

        DB::table('Trade')->where('Id', $trade->Id)->update([
            'State' => 2,
            'PayTime' => time()
        ]);
    }

    /**
     * @method 确认收款
     */
    public function Collections(int $uid, $oid){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if(empty($oid)) throw new ArException(ArException::PARAM_ERROR);

        DB::beginTransaction();
        try{
            $trade = DB::table('Trade')
                ->where('MemberId', $uid)
                ->where('OrderNumber', $oid)
                ->where('Type', 2)
                ->first();
            if(empty($trade)) throw new ArException(ArException::SELF_ERROR,'您没有此订单');
    
            if($trade->State != 2) throw new ArException(ArException::SELF_ERROR,'订单还未支付');
            //扣掉冻结的余额
            DB::table('MemberCoin')->where('CoinId', $trade->CoinId)->where('MemberId', $uid)->decrement('Forzen', $trade->Number);
            //
            DB::table('Trade')->where('Id', $trade->Id)->update([
                'FinishTime' => time(),
                'State' => 3
            ]);
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
        
    }

    /**
     * @method 订单详情
     */
    public function Detail(int $uid, $oid){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if(empty($oid)) throw new ArException(ArException::PARAM_ERROR);

        $trade = DB::table('Trade')->where('MemberId', $uid)->where('OrderNumber', $oid)->first();
        if(empty($trade)) throw new ArException(ArException::SELF_ERROR,'您没有此订单');

        $setting = DB::table('PlatformSetting')->first();
        if(empty($setting)) throw new ArException(ArException::SELF_ERROR,'暂时不能查看');

        $coin = Coin::where('Id', $setting->TradeCoinId)->first();
        if(empty($coin)) throw new ArException(ArException::SELF_ERROR,'交易币种不存在');

        //支付信息
        $payInfo = [];
        if($trade->PayMethod == 1){
            if($trade->Type == 1)
                $info = DB::table('BankCard')->where('MemberId', $trade->MerchantId)->where('Type',2)->first();
            else
                $info = DB::table('BankCard')->where('MemberId', $trade->MerchantId)->where('Type',1)->first();
            if(!empty($info)){
                $payInfo = [
                    'Name' => $info->Name,
                    'CardNo' => $info->CardNo,
                    'Bank' => $info->Bank
                ];
            }
        } else if($trade->PayMethod == 2){
            if($trade->Type == 1)
                $info = DB::table('BindPay')->where('MemberId', $trade->MerchantId)->where('Type', 2)->where('PayType', 2)->first();
            else 
                $info = DB::table('BindPay')->where('MemberId', $trade->MerchantId)->where('Type', 1)->where('PayType', 2)->first();
            if(!empty($info)){
                $payInfo = [
                    'NickName' => $info->NickName,
                    'Account' => $info->Account,
                    'QrCode' => $this->QiniuDomain().$info->QrCode,
                ];
            }
        } else if($trade->PayMethod == 3){
            if($trade->Type == 1)
                $info = DB::table('BindPay')->where('MemberId', $trade->MerchantId)->where('Type', 2)->where('PayType', 1)->first();
            else
                $info = DB::table('BindPay')->where('MemberId', $trade->MerchantId)->where('Type', 1)->where('PayType', 1)->first();
            if(!empty($info)){
                $payInfo = [
                    'NickName' => $info->NickName,
                    'Account' => $info->Account,
                    'QrCode' => $this->QiniuDomain().$info->QrCode,
                ];
            }
        }

        $data = [
            'State' => $trade->State,
            'AddTime' => $trade->AddTime,
            'TradeCancleTime' => $setting->TradeCancleTime,
            'CoinName' => $coin->EnName,
            'Type' => $trade->Type,
            'Number' => $trade->Number,
            'Price' => $trade->PayPrice,
            'UnitPrice' => bcdiv($trade->PayPrice, $trade->Number, 10),
            'PayMethod' => $trade->PayMethod,
            'RemarkCode' => $trade->RemarkCode,
            'PayInfo' => $payInfo,
            'AddTime' => $trade->AddTime,
            'OrderNumber' => $trade->OrderNumber,
            'PayTime' => $trade->PayTime,
            'FinishTime' => $trade->FinishTime
        ];
        return $data;
    }

    /**
     * @method 订单记录
     */
    public function List(int $uid, int $type, int $count){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if($count <= 0) throw new ArException(ArException::PARAM_ERROR);
        
        if($type == 0){
            $data = DB::table('Trade')->where('MemberId', $uid)->orderBy('Id','desc')->paginate($count);
        } else {
            $data = DB::table('Trade')->where('MemberId', $uid)->where('Type', $type)->orderBy('Id','desc')->paginate($count);            
        }
        $setting = DB::table('PlatformSetting')->first();
        if(empty($setting)) throw new ArException(ArException::SELF_ERROR,'暂时不能查看');
        $coin = Coin::where('Id', $setting->TradeCoinId)->first();
        if(empty($coin)) throw new ArException(ArException::SELF_ERROR,'交易币种不存在');
        $list = [];
        foreach($data as $item){
            $list[] = [
                'OrderNumber' => $item->OrderNumber,
                'State' => $item->State,
                'AddTime' => $item->AddTime,
                'Type' => $item->Type,
                'Number' => $item->Number,
                'CoinName' => $coin->EnName,
                'Money' => $item->PayPrice
            ];
        }
        return ['List' => $list, 'Total' => $data->total()];
    }

    /**
     * @method 购买
     */
    public function Purchase(int $uid, $number, int $method ){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if(!is_numeric($number)) throw new ArException(ArException::SELF_ERROR,'购买数量错误');
        
        if(!in_array($method, [1,2,3])) throw new ArException(ArException::SELF_ERROR,'支付方式错误');

        DB::beginTransaction();
        try{
            $setting = DB::table('PlatformSetting')->first();
            if(empty($setting)) throw new ArException(ArException::SELF_ERROR,'暂时不能购买');
            if(bccomp($setting->MinPurchaseNumber, $number, 10) > 0)
                throw new ArException(ArException::SELF_ERROR,'最小购买数量'.$setting->MinPurchaseNumber);

            if(bccomp($setting->MaxPurchaseNumber, $number, 10) < 0)
                throw new ArException(ArException::SELF_ERROR,'最大购买数量'.$setting->MaxPurchaseNumber);

            $price = bcmul($setting->TradeCoinPrice, $number, 10);
            //随机选取一个商户
            $merchant = DB::table('RandMerchat')->first();
            if(empty($merchant)) throw new ArException(ArException::SELF_ERROR,'暂无商户');
            $id = DB::table('Trade')->insertGetId([
                'MemberId' => $uid,
                'Number' => $number,
                'PayMethod' => $method,
                'OrderNumber' => md5(mt_rand(100000, 999999).md5(microtime())),
                'RemarkCode' => mt_rand(100000, 999999),
                'MerchantId' => $merchant->Id,
                'Type' => 1,
                'MerchantName' => $merchant->Name,
                'CoinId' => $setting->TradeCoinId,
                'AddTime' => time(),
                'PayPrice' => $price
            ]);
            $oid = date('YmdHi').$id;
            DB::table('Trade')->where('Id', $id)->update(['OrderNumber' => $oid]);
            DB::commit();
            return $oid;
        } catch(\Exception $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR,$e->getMessage());
        }
        
    }

    /**
     * @method 卖
     */
    public function Sell(int $uid, $number, int $method){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if(!is_numeric($number)) throw new ArException(ArException::SELF_ERROR,'卖出数量错误');

        if(!in_array($method, [1,2,3])) throw new ArException(ArException::SELF_ERROR,'支付方式错误');
        
        $member = Members::where('Id', $uid)->first();
        if(empty($member)) throw new ArException(ArException::USER_NOT_FOUND);

        if($method == 1 && $member->IsBindBank != 1)
            throw new ArException(ArException::SELF_ERROR,'请先绑定银行卡');
        
        if($method == 2 && $member->IsBindAlipay != 1)
            throw new ArException(ArException::SELF_ERROR,'请先绑定支付宝');

        if($method == 3 && $member->IsBindWx != 1)
            throw new ArException(ArException::SELF_ERROR,'请先绑定微信');

        DB::beginTransaction();
        try{
            $setting = DB::table('PlatformSetting')->first();
            if(empty($setting)) throw new ArException(ArException::SELF_ERROR,'暂时不能购买');

            $coin = Coin::where('Id', $setting->TradeCoinId)->first();
            if(empty($coin)) throw new ArException(ArException::SELF_ERROR,'交易币种不存在');

            if(bccomp($setting->MinPurchaseNumber, $number, 10) > 0)
                throw new ArException(ArException::SELF_ERROR,'最小卖出数量'.$setting->MinPurchaseNumber);

            if(bccomp($setting->MaxPurchaseNumber, $number, 10) < 0)
                throw new ArException(ArException::SELF_ERROR,'最大卖出数量'.$setting->MaxPurchaseNumber);

            $member = Members::find($uid);
            if(empty($member)) throw new ArException(ArException::USER_NOT_FOUND);
            
            if(!$member->IsBindBank && !$member->IsBindWx && !$member->IsBindAlipay)
                throw new ArException(ArException::SELF_ERROR,'请先绑定支付方式');

            $memberCoin = MemberCoin::where('CoinId', $setting->TradeCoinId)->where('MemberId', $uid)->first();
            if(empty($memberCoin)) throw new ArException(ArException::SELF_ERROR,'您未持有'.$coin->EnName);

            if(bccomp($memberCoin->Money, $number, 10) < 0)
                throw new ArException(ArException::SELF_ERROR,$coin->EnName.'余额不足');
            
            //冻结余额
            DB::table('MemberCoin')->where('MemberId', $uid)->where('CoinId', $setting->TradeCoinId)->update([
                'Money' => DB::raw("Money - {$number}"),
                'Forzen' => DB::raw("Forzen + {$number}")
            ]);
            self::AddLog($uid, -$number, $coin, 'trade_sell');

            //下单
            $price = bcmul($setting->TradeCoinSellPrice, $number, 10);
            $id = DB::table('Trade')->insertGetId([
                'MemberId' => $uid,
                'Number' => $number,
                'PayMethod' => $method,
                'OrderNumber' => md5(mt_rand(100000, 999999).md5(microtime())),
                'RemarkCode' => mt_rand(100000, 999999),
                'MerchantId' => $uid,
                'Type' => 2,
                'MerchantName' => $member->NickName,
                'CoinId' => $setting->TradeCoinId,
                'AddTime' => time(),
                'PayPrice' => $price
            ]);
            $oid = date('YmdHi').$id;
            DB::table('Trade')->where('Id', $id)->update(['OrderNumber' => $oid]);
            
            DB::commit();
            return $oid;
        } catch(\Exception $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

}