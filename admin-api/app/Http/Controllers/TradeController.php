<?php

namespace App\Http\Controllers;

use App\Exceptions\ArException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\CoinModel as Coin;
use App\Models\MembersCoinModel as MemberCoin;
use App\Models\FinancingMoldModel as FinancingMold;
use Illuminate\Support\Facades\Validator;

class TradeController extends Controller
{
    //确认收款
    public function Confirm(Request $request){
        $id = intval($request->input('Id'));
        if($id <= 0) throw new ArException(ArException::SELF_ERROR,'参数错误');

        $trade = DB::table('Trade')->where('Id', $id)->first();
        if(empty($trade)) throw new ArException(ArException::SELF_ERROR, '订单错误');

        if($trade->Type != 1) throw new ArException(ArException::SELF_ERROR,'非法操作');

        if($trade->State != 2) throw new ArException(ArException::SELF_ERROR,'订单状态错误');

        DB::beginTransaction();
        try{
            $coin = DB::table('Coin')->where('Id', $trade->CoinId)->first();
            DB::table('Trade')->where('Id', $id)->update(['State' => 3]);
            $memberCoin = DB::table('MemberCoin')
                ->where('MemberId', $trade->MemberId)
                ->where('CoinId', $trade->CoinId)
                ->first();
            if(empty($memberCoin)){
                DB::table('MemberCoin')->insert([
                    'MemberId' => $trade->MemberId,
                    'CoinId' => $trade->CoinId,
                    'Money' => $trade->Number,
                    'CoinName' => $coin->EnName
                ]);
            } else {
                DB::table('MemberCoin')
                    ->where('MemberId', $trade->MemberId)
                    ->where('CoinId', $trade->CoinId)
                    ->increment('Money', $trade->Number);
            }
            $fina = DB::table('financingmold')->where('call_index', 'trade_buy')->first();
            $memberCoin = DB::table('MemberCoin')
                ->where('MemberId', $trade->MemberId)
                ->where('CoinId', $trade->CoinId)
                ->first();
            $sort = $trade->MemberId % 20;
            if($sort < 10) $sort = '0'.$sort;
            $table = 'FinancingList_'.$sort;
            $data = [
                'MemberId' => $trade->MemberId,
                'Money' => $trade->Number,
                'CoinId' => $coin->Id,
                'CoinName' => $coin->EnName,
                'Mold' => $fina->id,
                'MoldTitle' => $fina->title,
                'Remark' => $fina->title,
                'AddTime' => time(),
                'Balance' => $memberCoin->Money
            ];
            DB::table($table)->insert($data);
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR,'网络错误，请稍后再试');
        }
        return self::returnMsg();
    }

    //确认支付
    public function Pay(Request $request){
        $id = intval($request->input('Id'));
        if($id <= 0) throw new ArException(ArException::SELF_ERROR,'参数错误');

        $trade = DB::table('Trade')->where('Id', $id)->first();
        if(empty($trade)) throw new ArException(ArException::SELF_ERROR, '订单错误');

        if($trade->Type != 2) throw new ArException(ArException::SELF_ERROR,'非法操作');

        if($trade->State != 1) throw new ArException(ArException::SELF_ERROR,'订单状态错误');

        DB::beginTransaction();
        try{
            // DB::table('MemberCoin')
            //     ->where('MemberId', $trade->MemberId)
            //     ->where('CoinId', $trade->CoinId)
            //     ->decrement('Forzen', $trade->Number);
            DB::table('Trade')->where('Id', $id)->update(['State' => 2, 'PayTime' => time()]);
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR,'网络错误，请稍后再试');
        }
        return self::returnMsg();
    }

    //取消订单
    public function Cancle(Request $request){
        $id = intval($request->input('Id'));
        if($id <= 0) throw new ArException(ArException::SELF_ERROR,'参数错误');

        $trade = DB::table('Trade')->where('Id', $id)->first();
        if(empty($trade)) throw new ArException(ArException::SELF_ERROR, '订单错误');

        if($trade->Type != 2) throw new ArException(ArException::SELF_ERROR,'非法操作');

        if($trade->State != 1) throw new ArException(ArException::SELF_ERROR,'订单状态错误');

        DB::beginTransaction();
        try{
            $coin = DB::table('Coin')->where('Id', $trade->CoinId)->first();
            DB::table('MemberCoin')
                ->where('MemberId', $trade->MemberId)
                ->where('CoinId', $trade->CoinId)
                ->update([
                    'Forzen' => DB::raw("Forzen - {$trade->Number}"),
                    'Money' => DB::raw("Money + {$trade->Number}")
                ]);
            $fina = DB::table('financingmold')->where('call_index', 'trade_cancle')->first();
            $sort = $trade->MemberId % 20;
            if($sort < 10) $sort = '0'.$sort;
            $table = 'FinancingList_'.$sort;
            $memberCoin = DB::table('MemberCoin')
                ->where('MemberId', $trade->MemberId)
                ->where('CoinId', $trade->CoinId)
                ->first();
            $data = [
                'MemberId' => $trade->MemberId,
                'Money' => $trade->Number,
                'CoinId' => $coin->Id,
                'CoinName' => $coin->EnName,
                'Mold' => $fina->id,
                'MoldTitle' => $fina->title,
                'Remark' => $fina->title,
                'AddTime' => time(),
                'Balance' => $memberCoin->Money
            ];
            DB::table($table)->insert($data);
            DB::table('Trade')->where('Id', $id)->update(['State' => 4,'FinishTime' => time()]);
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR,'网络错误，请稍后再试');
        }
        return self::returnMsg();

    }

    //订单详情
    public function Detail(Request $request){
        $id = intval($request->input('Id'));
        $trade = DB::table('Trade')->where('Id', $id)->first();
        if(empty($trade)) throw new ArException(ArException::SELF_ERROR,'订单不存在');
        $trade->AddTime = date('Y-m-d H:i:s', $trade->AddTime);
        $trade->PayTime = $trade->PayTime > 0 ? date('Y-m-d H:i:s', $trade->PayTime) : '未支付';
        $trade->FinishTime = $trade->FinishTime > 0 ? date('Y-m-d H:i:s', $trade->FinishTime) : '未完成';

        $coinName = '';
        $coin = DB::table('Coin')->where('Id', $trade->CoinId)->first();
        if(!empty($coin)) $coinName = $coin->EnName;
        $trade->CoinName = $coinName;
        $pay = '';
        $bank = '';
        $recName = '';
        if($trade->Type == 2){
            switch($trade->PayMethod){
                case 1:
                    //银行卡
                    $card = DB::table('BankCard')->where('MemberId', $trade->MemberId)->where('Type', 1)->first();
                    if(!empty($card)){
                        $pay = $card->CardNo;
                        $bank = $card->Bank;
                        $recName = $card->Name;
                    }
                    break;
                case 2:
                    //支付宝
                    $alipay = DB::table('BindPay')->where('Type', 1)->where('PayType', 2)->where('MemberId', $trade->MemberId)->first();
                    if(!empty($alipay)){
                        $pay = $alipay->QrCode;
                        $recName = $alipay->NickName;
                    }
                    break;
                case 3:
                    //微信
                    $wechat = DB::table('BindPay')->where('Type', 1)->where('PayType', 1)->where('MemberId', $trade->MemberId)->first();
                    if(!empty($wechat)){
                        $pay = $wechat->QrCode;
                        $recName = $wechat->NickName;
                    }
                    break;
            }
        }
        $trade->Pay = $pay;
        $trade->Bank = $bank;
        $trade->RecName = $recName;
        return self::returnMsg($trade);
    }
//今日求购量
    public function SettingByAmount(Request $request){
        $data=$request->post();
        if (!is_numeric($data['buy_amount'])) return self::returnMsg([],'输入格式错误，只能为纯数字',20001);
        $data=[
            'v'=>$data['buy_amount']
        ];
        DB::table('setting')->where('k','buy_amount')->update($data);
        return self::returnMsg([],'操作成功！',20000);
    }
    public function CTCList(Request $request){
//        $where=[];
        $limit = intval($request->input('limit'));
        $data = DB::table('CTCTrade');
        $Phone=$request->input('Phone','');
        $sPhone=$request->input('sPhone','');
        if (!empty($Phone)){
            $Id=DB::table('Members')->where('Phone',$Phone)->value('Id');
//               dd($Id);
            $data = $data->where('MemberId',$Id);
        }
        if (!empty($sPhone)){
            $Id=DB::table('Members')->where('Phone',$sPhone)->value('Id');
//               dd($Id);
            $data = $data->where('OrderMemberId',$Id);
        }
        if(!empty(trim($request->input('OrderSn')))){
            $data = $data->where('OrderSn', trim($request->input('OrderSn')));
        }
        if(!empty(trim($request->input('RemarkCode')))){
            $data = $data->where('RemarkCode', trim($request->input('RemarkCode')));
        }
        $data = $data->orderBy('Id','desc')->paginate($limit);

        $list = [];
        foreach($data as $item){

            $Str=substr($item->Imgs,1,strlen($item->Imgs)-2);  //去掉第一个字符和最后一个字符
            $strs = str_replace("\"","",$Str );
            $ImgList = explode(",", $strs);
            $temp = [];
            foreach ($ImgList as $pic_key => $pic) {
                $temp[] = $this->config['Domain'] . $pic;
            }
            $item->ImgListUrl = $temp;
            $item->ImgList = $ImgList;
            $order = DB::table('CTCOrder')->where('Id', $item->OrderId)->first();
            $coin = DB::table('Coin')->where('Id', $item->CoinId)->first();
            if (!empty( $coin->EnName)){
            $item->CoinName = $coin->EnName;
            }
            if($item->Type == 1){
                $sellMember = DB::table('Members')->where('Id', $item->OrderMemberId)->get();
                $buyMember = DB::table('Members')->where('Id', $item->MemberId)->get();
            } else {
                $buyMember = DB::table('Members')->where('Id', $item->OrderMemberId)->get();
                $sellMember = DB::table('Members')->where('Id', $item->MemberId)->get();
            }

            foreach ($sellMember as $v){
                $item->SellMember = $v->NickName;
                $item->SellPhone = $v->Phone;
            }
            foreach ($buyMember as $k){
                $item->BuyMember = $k->NickName;
                $item->BuyPhone = $k->Phone;
            }
//            $item->SellMember = $sellMember->NickName;
//            $item->BuyMember = $buyMember->NickName;
//            $item->Fee = $item->Type == 1 ? bcmul($item->RecvFee, $item->Number, 4) : bcmul($item->Fee, $item->Number, 4);
            $list[] = $item;
        }
        $ctc_tx_fee=DB::table('setting')->where('k','ctc_tx_fee')->value('v');
        $res=[];
//        $res = ['list' => $list, 'total' => $data->total()];
        $res["total"] =$data->total();
        $res["list"] = $list;
        $res["ctc_tx_fee"] = $ctc_tx_fee;
        return self::returnMsg($res);
    }

    public function CTCOrder(Request $request){
        $where = [];
        $Phone=$request->input('Phone');
        $member_where = [];
        if ($Phone) {
                $member_where[] = ["Phone", "=", $Phone];
        }
        if (!empty($member_where)) {
            $MemberId = DB::table("Members")->where($member_where)->value("Id");
            $where[] = ["MemberId", "=", $MemberId];
        }
        $limit = intval($request->input('limit'));
        $data = DB::table('CTCOrder')->where($where)->orderBy('Id', 'desc')->paginate($limit);
        $list = [];
        foreach($data as $item){
            $member = DB::table('Members')->where('Id', $item->MemberId)->first();
            if(!empty($member->NickName)){
            $item->NickName = $member->NickName;
            }
            if (!empty($member->Phone)){
            $item->Phone = $member->Phone;

            }
            $list[] = $item;
        }
        $buy_amount = DB::table('CTCOrder')
            ->where('State',0)
            ->where('Type', 2)
            ->sum('Number');
        $res["Number"] = $buy_amount;
        $res = ['list' => $list, 'total' => $data->total(),'Number'=>$buy_amount];
        return self::returnMsg($res);
    }

    //订单列表
    public function List(Request $request){
        $limit = intval($request->input('limit'));
        $data = DB::table('Trade');
        $buy = DB::table('Trade');
        $sell = DB::table('Trade');
        if(!empty($request->input('State'))){
            $data->where('State', intval($request->input('State')));
        }
        if(!empty($request->input('Type'))){
            $data->where('Type', intval($request->input('Type')));
            $buy->where('Type', intval($request->input('Type')));
            $sell->where('Type', intval($request->input('Type')));
        }
        if(!empty($request->input('AddTime'))){
            $date[0] = strtotime($request->input('AddTime')[0]);
            $date[1] = strtotime($request->input('AddTime')[1]);
            $data = $data->whereBetween('AddTime', $date);
            $buy = $buy->whereBetween('AddTime', $date);
            $sell = $sell->whereBetween('AddTime', $date);
        }
        if(!empty(trim($request->input('Merchant')))){
            $data = $data->where('MerchantName', trim($request->input('Merchant')))->where('Type', 1);
            $buy = $buy->where('MerchantName', trim($request->input('Merchant')))->where('Type', 1);
            $sell = $sell->where('MerchantName', trim($request->input('Merchant')))->where('Type', 1);
        }
        if(!empty(trim($request->input('MemberName')))){
            $member = DB::table('Members')->where('NickName', trim($request->input('MemberName')))->get()->toArray();
            $mids = array_column($member, 'Id');
            $data = $data->whereIn('MemberId', $mids);
            $buy = $buy->whereIn('MemberId', $mids);
            $sell = $sell->whereIn('MemberId', $mids);
        }
        if(!empty(trim($request->input('Phone')))){
            $member = DB::table('Members')->where('Phone', trim($request->input('Phone')))->get()->toArray();
            $mids = array_column($member, 'Id');
            $data = $data->whereIn('MemberId', $mids);
            $buy = $buy->whereIn('MemberId', $mids);
            $sell = $sell->whereIn('MemberId', $mids);
        }
        if(!empty(trim($request->input('OrderNumber')))){
            $data = $data->where('OrderNumber', trim($request->input('OrderNumber')));
            $buy = $buy->where('OrderNumber', trim($request->input('OrderNumber')));
            $sell = $sell->where('OrderNumber', trim($request->input('OrderNumber')));
        }
        if(!empty(trim($request->input('RemarkCode')))){
            $data = $data->where('RemarkCode', trim($request->input('RemarkCode')));
            $buy = $buy->where('RemarkCode', trim($request->input('RemarkCode')));
            $sell = $sell->where('RemarkCode', trim($request->input('RemarkCode')));
        }

        $trades = $data->orderBy('Id','desc')->paginate($limit);
        $sell = $sell->where('type', 2)->where('State','<>',4)->sum('Number');
        $buy = $buy->where('type', 1)->where('State','<>',4)->sum('Number');

        $list = [];
        foreach($trades as $item){
            $phone = '';
            $nickName = '';
            $member = DB::table('Members')->where('Id', $item->MemberId)->first();
            if(!empty($member)){
                $phone = $member->Phone;
                $nickName = $member->NickName;
            }
            $item->Phone = $phone;
            $item->NickName = $nickName;
            $item->Account = $this->GetPayAccount($item);

            $list[] = $item;
        }

        $res = ['list' => $list, 'total' => $trades->total(),'buy' => $buy, 'sell' => $sell];
        return self::returnMsg($res);
    }

    //处理申诉
    public function AppealHandle(Request $request){
        $id = intval($request->input('Id'));
        $type = intval($request->input('Type'));
        //type=2未付款  type=1 已付款
        if(!in_array($type, [1,2]))
            throw new ArException(ArException::SELF_ERROR,'参数错误');
        if($id <= 0) throw new ArException(ArException::PARAM_ERROR);

        $appeal = DB::table('CTCAppeal')->where('Id', $id)->first();

        if($type == 2){
            DB::beginTransaction();
            try{
                DB::table('CTCTrade')->where('Id', $appeal->TradeId)->where('State', 5)->update([
                    'State' => 0,
                    'PayTime' => 0
                ]);
                DB::table('CTCAppeal')->where('TradeId', $appeal->TradeId)->update(['Result' => 2]);
                DB::commit();
            } catch(\Exception $e){
                DB::rollBack();
                throw new ArException(ArException::SELF_ERROR, $e->getMessage());
            }

            return self::returnMsg();
        } else {
            DB::beginTransaction();
            try{
                $appeal = DB::table('CTCAppeal')->where('Id', $id)->where('Result', 0)->first();
                if(empty($appeal)) throw new ArException(ArException::SELF_ERROR,'申诉已处理完成，请勿重复处理');
                $trade = DB::table('CTCTrade')->where('Id', $appeal->TradeId)->where('State', 5)->first();
                if(empty($trade)) throw new ArException(ArException::SELF_ERROR,'交易不存在');

                $order = DB::table('CTCOrder')->where('Id', $trade->OrderId)->first();
                if(empty($order)) throw new ArException(ArException::SELF_ERROR,'订单不存在');

                $buyMember = 0;
                $sellMember = 0;
                if($trade->Type == 1){
                    $buyMember = $trade->MemberId;
                    $sellMember = $trade->OrderMemberId;
                    $fee = $order->Fee;
                    $recvFee = $trade->RecvFee;
                } else {
                    $buyMember = $trade->OrderMemberId;
                    $sellMember = $trade->MemberId;
                    $fee = $trade->Fee;
                    $recvFee = $order->RecvFee;
                }

                $setting = DB::table('CTCSetting')->first();
                if(empty($setting)) throw new ArException(ArException::SELF_ERROR,'系统错误');

                $coin =  DB::table('Coin')->where('Id', $trade->CoinId)->first();
                //扣除手续费
                $recvFee = bcmul($trade->Number, $recvFee, 10);
                $memberCoin = DB::table('MemberCoin')->where('CoinId', $trade->CoinId)->where('MemberId', $buyMember)->first();
                $Forzen = DB::table('MemberCoin')
                    ->where('MemberId', $sellMember)
                    ->where('CoinId', $trade->CoinId)
                    ->value('Forzen');
                if ($Forzen < bcadd($trade->Number, $fee, 10)) return self::returnMsg([],'因卖家冻结余额不足，无法点击已付款',20001);
                //把币拨给买家
                $coin = Coin::where('Id', $trade->CoinId)->first();
                if(empty($memberCoin)){
                    DB::table('MemberCoin')->insert([
                        'MemberId' => $buyMember,
                        'CoinId' => $trade->CoinId,
                        'CoinName' => $coin->EnName,
                        'Money' => bcsub($trade->Number, $recvFee, 10)
                    ]);
                } else {
                    DB::table('MemberCoin')
                        ->where('MemberId', $buyMember)
                        ->where('CoinId', $trade->CoinId)
                        ->increment('Money', bcsub($trade->Number, $recvFee, 10));
                }
                self::AddLog($buyMember, bcsub($trade->Number, $recvFee, 10), $coin, 'ctc_buy');
                //扣除卖家冻结的部分 扣除卖家一部分手续费
                $fee = bcmul($trade->Number, $fee, 10);
                DB::table('MemberCoin')
                    ->where('MemberId', $sellMember)
                    ->where('CoinId', $trade->CoinId)
                    ->decrement('Forzen', bcadd($trade->Number, $fee, 10));
                DB::table('CTCTrade')->where('Id', $appeal->TradeId)->where('State', 1)->update([
                    'State' => 2,
                    'FinishTime' => time()
                ]);
                DB::table('CTCTrade')->where('Id', $appeal->TradeId)->update([
                    'State' => 2,
                    'FinishTime' => time()
                ]);
                DB::table('CTCAppeal')->where('TradeId', $appeal->TradeId)->update(['Result' => 1]);
                DB::commit();
            } catch(\Exception $e){
                DB::rollBack();
                throw new ArException(ArException::SELF_ERROR, $e->getMessage());
            }
            return self::returnMsg();
        }
    }

    public function CTCCancle(Request $request){
        $id = intval($request->input('Id'));
        if($id <= 0) throw new ArException(ArException::PARAM_ERROR);

        DB::beginTransaction();
        try{
            $trade = DB::table('CTCTrade')->where('Id', $id)->where('State', 0)->first();
            if(empty($trade)) throw new ArException(ArException::SELF_ERROR,'交易不存在或已取消');

            $order = DB::table('CTCOrder')->where('Id', $trade->OrderId)->first();
            if(empty($order)) throw new ArException(ArException::SELF_ERROR,'订单错误');

            $fee = bcmul($order->Fee, $trade->Number, 10);
            $number = bcadd($fee, $trade->Number, 10);

            $coin = Coin::where('Id', $trade->CoinId)->first();
            if($trade->Type == 1){
                //买单取消
                DB::table('MemberCoin')->where('MemberId', $order->MemberId)->where('CoinId', $trade->CoinId)->update([
                    'Money' => DB::raw("Money+{$number}"),
                    'Forzen' => DB::raw("Forzen-{$number}"),
                ]);
                self::AddLog($order->MemberId, $number, $coin, 'ctc_sell_cancle');
            } else {
                $fee = bcmul($trade->Fee, $trade->Number, 10);
                $number = bcadd($fee, $trade->Number, 10);
                DB::table('MemberCoin')->where('MemberId', $trade->MemberId)->where('CoinId', $trade->CoinId)->update([
                    'Money' => DB::raw("Money+{$number}"),
                    'Forzen' => DB::raw("Forzen-{$number}")
                ]);
                self::AddLog($trade->MemberId, $number, $coin, 'ctc_sell_cancle');
            }
            DB::table('CTCTrade')->where('Id', $id)->where('State', 0)->update(['State' =>  3, 'FinishTime' => time()]);
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
        return self::returnMsg();
    }

    public function StopOrder(Request $request){
        $id = intval($request->input('Id'));
        if($id <= 0) throw new ArException(ArException::PARAM_ERROR);

        DB::beginTransaction();
        try{
            $order = DB::table('CTCOrder')->where('Id', $id)->where('State', 0)->first();
            if(empty($order)) throw new ArException(ArException::SELF_ERROR,'订单不存在或已终止');
            $uid = $order->MemberId;

            $coin = Coin::where('Id', $order->CoinId)->first();
            if($order->Type == 1){
                //出售订单返回剩余数量和一部分手续费
                $fee = bcmul($order->Fee, $order->SurplusNumber, 10);
                $number = bcadd($fee, $order->SurplusNumber, 10);
                DB::table('MemberCoin')->where('MemberId', $uid)->where('CoinId', $order->CoinId)->update([
                    'Forzen' => DB::raw("Forzen-{$number}"),
                    'Money' => DB::raw("Money+{$number}")
                ]);
                self::AddLog($uid, $number, $coin, 'ctc_close_order');
            }
            DB::table('CTCOrder')->where('Id', $id)->update(['State' => 1]);
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
        return self::returnMsg();
    }

    //加日志
    protected static function AddLog(int $uid, $money, Coin $coin, string $mold){
        $sort = $uid % 20;
        if($sort < 10) $sort = '0'.$sort;
        $table = 'FinancingList_'.$sort;
        $fina = FinancingMold::where('call_index', $mold)->first();
        if(empty($fina)) return ;
        $memberCoin = MemberCoin::where('MemberId', $uid)->where('CoinId', $coin->Id)->first();
        if(empty($memberCoin)) throw new ArException(ArException::UNKONW);
        if(bccomp($memberCoin->Money, 0, 10) < 0) throw new ArException(ArException::COIN_NOT_ENOUGH);
        $data = [
            'MemberId' => $uid,
            'Money' => $money,
            'CoinId' => $coin->Id,
            'CoinName' => $coin->EnName,
            'Mold' => $fina->id,
            'MoldTitle' => $fina->title,
            'Remark' => $fina->title,
            'AddTime' => time(),
            'Balance' => $memberCoin->Money
        ];
        DB::table($table)->insert($data);
    }
//申诉列表
    public function Appeal(Request $request){
        $limit = intval($request->input('limit'));
        $orderSn = trim($request->input('OrderSn'));
        if(!empty($orderSn)){
            $trade = DB::table('CTCTrade')->where('OrderSn', $orderSn)->get()->toArray();
            $tids = array_column($trade, 'Id');
            $data = DB::table('CTCAppeal')->whereIn('TradeId', $tids)->paginate($limit);
        } else {
            $data = DB::table('CTCAppeal')->paginate($limit);
        }
        $list = [];
        $qiniu = DB::table('qiniuconfig')->get();
        foreach ($qiniu as $v){
            $Domain=$v->Domain;
        }
//        foreach ($data as $key => &$v) {
//            $Imgs = json_decode($v->Imgs);
//            $temp = [];
//            foreach ($Imgs as $pic_key => $pic) {
//                $temp[] = $this->config['Domain'] . $pic;
//            }
//            $v->Imgs = $Imgs;
//            $v->ImgsUrl = $temp;
//        }
        foreach($data as $item){
            $Imgs = json_decode($item->Imgs);
            if (is_array($Imgs)){
            $temp = [];
            foreach ($Imgs as $pic_key => $pic) {
                $temp[] = $this->config['Domain'] . $pic;
            }
            $item->Imgs = $Imgs;
            $item->ImgsUrl = $temp;
            }
//            $item->Imgs = explode(",", $item->Imgs);
//            foreach($item->Imgs as $k => $img){
//                $item->Imgs[$k] = $Domain.$img;
//            }
            $member = DB::table('Members')->where('Id', $item->MemberId)->first();
            if(!empty($member->NickName)){
            $item->AppealName = $member->NickName;
            }
            if(!empty($member->Phone)){
            $item->Phone = $member->Phone;
            }
            $trade = DB::table('CTCTrade')->where('Id', $item->TradeId)->get();
            foreach ($trade as &$v){
                $OrderSn=$v->OrderSn;
                $CoinId=$v->CoinId;
                $Type=$v->Type;
                $OrderMemberId=$v->OrderMemberId;
                $MemberId=$v->MemberId;
                $SumPrice=$v->SumPrice;
                $Number=$v->Number;
                $RemarkCode=$v->RemarkCode;
                $Price=$v->Price;
            }
            $item->OrderSn = $OrderSn;
            $coin = DB::table('Coin')->where('Id', $CoinId)->first();
            if(!empty($coin->EnName)){
            $item->CoinName = $coin->EnName;
            }
            if($Type == 1){
                $sellMember = DB::table('Members')->where('Id', $OrderMemberId)->first();
                $buyMember = DB::table('Members')->where('Id', $MemberId)->first();
            } else {
                $buyMember = DB::table('Members')->where('Id', $OrderMemberId)->first();
                $sellMember = DB::table('Members')->where('Id', $MemberId)->first();
            }
            if(!empty($sellMember->NickName)){
            $item->SellMember = $sellMember->NickName;
            }
            if(!empty($buyMember->NickName)){
            $item->BuyMember = $buyMember->NickName;
            }
            $item->SumPrice = bcmul($SumPrice, 1, 5);
            $item->Number = bcmul($Number, 1, 5);
            $item->RemarkCode = $RemarkCode;
            $item->Price = bcmul($Price, 1, 5);

            $reason = DB::table('CTCAppealReason')->where('Id', $item->ReasonId)->first();
            $item->Reason = $reason->Content;

            $list[] = $item;
        }
        $res = ['list' => $list, 'total' => $data->total()];
        return self::returnMsg($res);
    }

    public function GetPayAccount($trade){
        switch($trade->PayMethod){
            case 1: //银行卡
                if($trade->Type == 1){   //买
                    $bank = DB::table('BankCard')->where('MemberId', $trade->MerchantId)->where('Type', 2)->first();
                } else {   //卖
                    $bank = DB::table('BankCard')->where('MemberId', $trade->MerchantId)->where('Type', 1)->first();
                }
                if(empty($bank)) return '';
                return $bank->CardNo;
                break;
            case 2: //支付宝
                if($trade->Type == 1){  //买
                    $pay = DB::table('BindPay')->where('MemberId', $trade->MerchantId)->where('Type', 2)->where('PayType', 2)->first();
                } else {  //卖
                    $pay = DB::table('BindPay')->where('MemberId', $trade->MerchantId)->where('Type', 1)->where('PayType', 2)->first();
                }
                if(empty($pay)) return '';
                return $pay->Account;
                break;
            case 3: //微信
                if($trade->Type == 1){  //买
                    $pay = DB::table('BindPay')->where('MemberId', $trade->MerchantId)->where('Type', 2)->where('PayType', 1)->first();
                } else {  //卖
                    $pay = DB::table('BindPay')->where('MemberId', $trade->MerchantId)->where('Type', 1)->where('PayType', 1)->first();
                }
                if(empty($pay)) return '';
                return $pay->Account;
               break;
            default:
                return '';
        }
    }
    //交易规则
   public function TradeRules(){
       $data =DB::table('ArticleList')->where('TypeTitle','交易规则')->first();
       if(empty($data)) return self::returnMsg(['ArticleDetails' => ''], '', 20000);
       return self::returnMsg(['ArticleDetails' => $data->ArticleDetails], '', 20000);
   }
   //修改交易规则
    public function TradeRulesEdit(Request $request){
        $content = $request->input('content');
        if(empty($content))
            throw new ArException(ArException::SELF_ERROR,'请填写内容');
        $data = DB::table('ArticleList')->where('TypeTitle','交易规则')->first();
        if(empty($data)){
            DB::table('ArticleList')->where('TypeTitle','交易规则')->insert([
                'ArticleDetails' => $content
            ]);
        } else {
            DB::table('ArticleList')->where('TypeTitle','交易规则')->update(['ArticleDetails' => $content]);
        }
        return self::returnMsg([], '', 20000);
    }
    //交易指导
    public function TradeGuidance(){
        $data =DB::table('ArticleList')->where('TypeTitle','交易指导')->first();
        if(empty($data)) return self::returnMsg(['ArticleDetails' => ''], '', 20000);
        return self::returnMsg(['ArticleDetails' => $data->ArticleDetails], '', 20000);
    }
    //修改交易指导
    public function TradeGuidanceEdit(Request $request){
        $content = $request->input('content');
        if(empty($content))
            throw new ArException(ArException::SELF_ERROR,'请填写内容');
        $data = DB::table('ArticleList')->where('TypeTitle','交易指导')->first();
        if(empty($data)){
            DB::table('ArticleList')->where('TypeTitle','交易指导')->insert([
                'ArticleDetails' => $content
            ]);
        } else {
            DB::table('ArticleList')->where('TypeTitle','交易指导')->update(['ArticleDetails' => $content]);
        }
        return self::returnMsg([], '', 20000);
    }
    //森林规则
    public function ForestRule(){
        $data =DB::table('Setting')->where('k','sapling_rule')->first();
        if(empty($data)) return self::returnMsg(['v' => ''],['k' => ''], '', 20000);
        return self::returnMsg(['v' => $data->v], '', 20000);
    }
    //修改交易指导
    public function ForestRulEdit(Request $request){
        $content = $request->input('content');
        if(empty($content))
            throw new ArException(ArException::SELF_ERROR,'请填写内容');
        $data = DB::table('Setting')->where('k','sapling_rule')->first();
        if(empty($data)){
            DB::table('Setting')->where('k','sapling_rule')->insert([
                'v' => $content
            ]);
        } else {
            DB::table('Setting')->where('k','sapling_rule')->update(['v' => $content]);
        }
        return self::returnMsg([], '', 20000);
    }
//    //交易手续费
//    public function TransactionFee(){
//        $data=DB::table('Setting')->where('k','ctc_tx_fee')->first();
//        if(empty($data)) return self::returnMsg(['v' => ''],['k' => ''], '', 20000);
//        return self::returnMsg(['v' => $data->v], '', 20000);
//    }
//    //交易手续费配置
//    public function TransactionFeeEdit(Request $request){
//
//        $content = $request->input('content');
//        if(empty($content))
//            throw new ArException(ArException::SELF_ERROR,'手续费不能为空');
//            DB::table('Setting')->where('k','ctc_tx_fee')->update(['v' => $content]);
//        return self::returnMsg([], '', 20000);
//
//    }
}
