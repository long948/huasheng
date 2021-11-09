<?php

namespace App\Http\Controllers;

use App\Exceptions\ArException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function InviteList(){
        $list = DB::table('InviteSetting')->get();
        return self::returnMsg($list);
    }

    //修改邀请收益
    public function InviteEdit(Request $request){
        $data = $request->input('data');
        DB::beginTransaction();
        try{
            foreach($data as $item){
                DB::table('InviteSetting')
                    ->where('Id', $item['Id'])
                    ->update(['Ratio' => $item['Ratio']]);
            }
            DB::commit();
        } catch(ArException $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, '修改失败-101');
        } catch(\Exception $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, '修改失败');
        }
        return self::returnMsg();
    }

    //全球分红和回购比例
    public function Other(){
        $data = DB::table('PlatformSetting')->first();
        return self::returnMsg($data);
    }

    public function CTC(){
        $data = DB::table('CTCSetting')->first();
        $data->IsAlipay = intval($data->IsAlipay);
        $data->IsBank = intval($data->IsBank);
        $data->IsWechat = intval($data->IsWechat);
        return self::returnMsg($data);
    }

    public function CTCCoin(){
        $data = DB::table('CTCCoinSetting')->get();
        $list = [];
        foreach($data as $item){
            $coin = DB::table('Coin')->where('Id', $item->CoinId)->get();
            foreach ($coin as $v){
                $EnName=$v->EnName;
            }
            $item->CoinId = intval($item->CoinId);
            $item->IsBuy = intval($item->IsBuy);
            $item->IsSell = intval($item->IsSell);
            $item->CoinName = $EnName;
            $list[] = $item;
        }
        return self::returnMsg($list);
    }

    public function CTCCoinAdd(Request $request){
        $rules = [
            'IsBuy' => 'required|integer',
            'IsSell' => 'required|integer',
            'CoinId' => 'required|integer',
        ];
        $valid = Validator::make($request->all(), $rules,[
            'IsBuy.required' => '请选择是否可求购',
            'IsSell.required' => '请选择是否可出售',
            'CoinId.required' => '请选择币种'
        ]);
        if($valid->fails())
            return self::errorMsg($valid->errors()->first());
        $data = $valid->validated();
        $has = DB::table('CTCCoinSetting')->where('CoinId', $data['CoinId'])->first();
        if(!empty($has)) throw new ArException(ArException::SELF_ERROR,'此币种已存在');
        DB::table('CTCCoinSetting')->insert($data);
        return self::returnMsg();
    }

    public function CTCCoinEdit(Request $request){
        $id = intval($request->input('Id'));
        if($id <= 0) throw new ArException(ArException::PARAM_ERROR);
        $rules = [
            'IsBuy' => 'required|integer',
            'IsSell' => 'required|integer',
            'CoinId' => 'required|integer',
        ];
        $valid = Validator::make($request->all(), $rules,[
            'IsBuy.required' => '请选择是否可求购',
            'IsSell.required' => '请选择是否可出售',
            'CoinId.required' => '请选择币种'
        ]);
        if($valid->fails())
            return self::errorMsg($valid->errors()->first());
        $data = $valid->validated();
        $has = DB::table('CTCCoinSetting')->where('Id', '<>', $id)->where('CoinId', $data['CoinId'])->first();
        if(!empty($has)) throw new ArException(ArException::SELF_ERROR,'此币种已存在');
        DB::table('CTCCoinSetting')->where('Id', $id)->update($data);
        return self::returnMsg();
    }

    public function CTCEdit(Request $request){
        $rules = [
            'MinMoney' => 'required|numeric',
            'MaxMoney' => 'required|numeric',
            'MaxSellOrder' => 'required|integer',
            'SellFee' => 'required|numeric',
            'RecvFee' => 'required|numeric',
            'CancleTime' => 'required|integer',
            'IsAlipay' => 'required|integer',
            'IsBank' => 'required|integer',
            'IsWechat' => 'required|integer',
            'FrozenTime' => 'required|integer',
            'MaxTrade' => 'required|integer',
            'start_time' => 'required|numeric',
            'end_time' => 'required|numeric'
        ];
        $valid = Validator::make($request->all(), $rules,[
            'MinMoney.required' => '请填写最低限额',
            'MaxMoney.required' => '请填写最高限额',
            'MaxSellOrder.required' => '请填写个人最多单数',
            'SellFee.required' => '请填写卖出手续费',
            'RecvFee.required' => '请填写收款手续费',
            'CancleTime.required' => '请填写超市取消时间',
            'IsAlipay.required' => '请选择是否支持支付宝',
            'IsBank.required' => '请选择是否支持银行卡',
            'IsWechat.required' => '请选择是否支持微信',
            'FrozenTime.required' => '请填写超市冻结时间',
            'MaxTrade.required' => '请填写个人订单上限',
            'start_time.required' => '请填写交易开始时间',
            'end_time.required' => '请填写交易结束时间'
        ]);
        if($valid->fails())
            return self::errorMsg($valid->errors()->first());
        $data = $valid->validated();
        DB::table('CTCSetting')->update($data);
        return self::returnMsg();
    }

    //编辑全球分红和回购比例
    public function OtherEdit(Request $request){
        $rules = [
            'RegProductId' => 'required|integer',
            'MinWithdraw' => 'required|numeric',
            'WithdrawFee' => 'required|numeric',
            'TradeCoinId' => 'required|numeric',
            'MinPurchaseNumber' => 'required|numeric',
            'MaxPurchaseNumber' => 'required|numeric',
            'TradeCoinPrice' => 'required|numeric',
            'TradeCancleTime' => 'required|integer',
            'AutoConfirm' => 'required|integer',
            'AuthReward' => 'required|numeric',
            'TradeCoinSellPrice' => 'required|numeric',
            'LimitNumber' => 'required|integer',
        ];
        $valid = Validator::make($request->all(), $rules,[
            'RegProductId.required' => '请选择注册送的矿机',
            'MinWithdraw.required' => '请填写矿机收益最小提现数量',
            'WithdrawFee.required' => '请填写矿机收益提现手续费',
            'TradeCoinId.required' => '请填写交易币种Id',
            'MinPurchaseNumber.required' => '请填写最小购买数量',
            'MaxPurchaseNumber.required' => '请填写最大购买数量',
            'TradeCoinPrice.required' => '请填写交易币种价格',
            'TradeCancleTime.required' => '请填写买单自动取消时间',
            'AutoConfirm.required' => '请填写卖单自动确认时间',
            'AuthReward.required' => '请填写空投奖励',
            'TradeCoinSellPrice.required' => '交易币种卖出数量',
            'LimitNumber.required' => '限制购买数量',
        ]);
        if($valid->fails())
            return self::errorMsg($valid->errors()->first());
        $data = $valid->validated();
        DB::table('PlatformSetting')->update($data);
        return self::returnMsg();
    }

}
