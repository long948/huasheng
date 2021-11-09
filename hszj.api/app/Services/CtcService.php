<?php


namespace App\Services;

use App\Events\UserTransactionAmountEvent;
use App\Exceptions\ArException;
use App\Libraries\SendAppNotify;
use Illuminate\Support\Facades\DB;
use App\Models\CoinModel as Coin;
use App\Models\SettingModel;
use Illuminate\Support\Facades\Redis;

class CtcService extends Service
{

    //申诉
    public function Appeal(int $uid, int $id, int $reasonId, string $content, string $imgs)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($id <= 0) throw new ArException(ArException::PARAM_ERROR);
        if ($reasonId <= 0) throw new ArException(ArException::PARAM_ERROR);
        if (empty($content)) throw new ArException(ArException::SELF_ERROR, '请填写内容');

        $order = DB::table('CTCTrade')->where(function ($query) use ($uid) {
            $query->where('OrderMemberId', $uid)
                ->orWhere('MemberId', $uid);
        })->where('Id', $id)->first();
        if (empty($order)) throw new ArException(ArException::SELF_ERROR, '订单不存在');
        if ($order->State != 1) throw new ArException(ArException::SELF_ERROR, '只能申诉待确认订单');

        $reason = DB::table('CTCAppealReason')->where('Id', $reasonId)->first();
        if (empty($reason)) throw new ArException(ArException::SELF_ERROR, '请选择申诉原因');

        $imgs = json_decode($imgs, true);
        // if(!is_array($imgs)) throw new ArException(ArException::SELF_ERROR,'请上传图片');

        // if(count($imgs) < 1 || count($imgs) > 3)
        //     throw new ArException(ArException::SELF_ERROR,'图片请上传1-3张');

        $has = DB::table('CTCAppeal')->where('TradeId', $id)->where('MemberId', $uid)->first();
        if (!empty($has)) throw new ArException(ArException::SELF_ERROR, '请勿重复操作');

        DB::beginTransaction();
        try {
            DB::table('CTCAppeal')->insert([
                'TradeId' => $id,
                'MemberId' => $uid,
                'AppealMemberId' => $order->MemberId == $uid ? $order->OrderMemberId : $order->MemberId,
                'Content' => $content,
                'ReasonId' => $reasonId,
                'Imgs' => json_encode($imgs),
                'AddTime' => time()
            ]);

            DB::table('CTCTrade')->where('State', 1)->where('Id', $id)->update([
                'State' => 5
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }

    }

    /**
     * @method 个人资料
     * @param int $id 订单Id
     */
    public function MemberInfo(int $id)
    {
        $order = DB::table('CTCOrder')->where('Id', $id)->first();
        if (empty($order)) throw new ArException(ArException::SELF_ERROR, '订单不存在');
        $member = DB::table('Members')->where('Id', $order->MemberId)->first();
        //
        $serviceMember = DB::table('CTCTrade')
            ->where('OrderMemberId', $order->MemberId)
            ->where('State', 2)
            ->count(DB::raw('DISTINCT(MemberId)'));

        $ratio = 0;
        $tradeNum = DB::table('CTCTrade')->where('OrderMemberId', $order->MemberId)->orWhere('MemberId', $order->MemberId)->count();
        if ($tradeNum > 0) {
            $success = DB::table('CTCTrade')->where('State', 2)->where(function ($query) use ($order) {
                $query->where('OrderMemberId', $order->MemberId)
                    ->orWhere('MemberId', $order->MemberId);
            })->count();
            $ratio = bcdiv($success, $tradeNum, 4);
        }
        $avgPay = DB::table('CTCTrade')
            ->where(function ($query) use ($order) {
                $query->where('OrderMemberId', $order->MemberId)
                    ->orWhere('MemberId', $order->MemberId);
            })
            ->where('State', 2)
            ->avg(DB::raw("PayTime-AddTime"));
        $avgConfrim = DB::table('CTCTrade')
            ->where(function ($query) use ($order) {
                $query->where('OrderMemberId', $order->MemberId)
                    ->orWhere('MemberId', $order->MemberId);
            })
            ->where('State', 2)
            ->avg(DB::raw("FinishTime-PayTime"));
        //
        $buyData = DB::table('CTCOrder')
            ->where('MemberId', $order->MemberId)
            ->where('State', 0)->where('Type', 1)->get();
        $buyList = [];
        foreach ($buyData as $item) {
            $coin = DB::table('Coin')->where('Id', $item->CoinId)->first();
            if (empty($coin)) continue;
            $buyList[] = [
                'Id' => $item->Id,
                'CoinName' => $coin->EnName,
                'IsBank' => $item->IsBank,
                'IsWechat' => $item->IsWechat,
                'IsAlipay' => $item->IsAlipay,
                'Price' => $item->Price,
                'MinMoney' => $item->MinMoney,
                'MaxMoney' => $item->MaxMoney,
                'Number' => $item->Number
            ];
        }
        $sellList = [];
        $sellData = DB::table('CTCOrder')
            ->where('MemberId', $order->MemberId)
            ->where('State', 0)->where('Type', 2)->get();
        foreach ($sellData as $item) {
            $coin = DB::table('Coin')->where('Id', $item->CoinId)->first();
            if (empty($coin)) continue;
            $sellList[] = [
                'Id' => $item->Id,
                'CoinName' => $coin->EnName,
                'IsBank' => $item->IsBank,
                'IsWechat' => $item->IsWechat,
                'IsAlipay' => $item->IsAlipay,
                'Price' => $item->Price,
                'MinMoney' => $item->MinMoney,
                'MaxMoney' => $item->MaxMoney,
                'Number' => $item->Number
            ];
        }
        $appeal = DB::table('CTCAppeal')->where('AppealMemberId', $order->MemberId)->count();
        $data = [
            'Name' => $member->NickName,
            'IsAuth' => $member->IsAuth,
            'Phone' => $member->Phone,
            'Avatar' => empty($member->Avatar) ? $member->Avatar : $this->QiniuDomain() . $member->Avatar,
            'RegTime' => $member->RegTime,
            'ServiceMember' => $serviceMember,
            'TradeNumber' => $tradeNum,
            'SuccessRatio' => $ratio,
            'AvgPayTime' => (float)$avgPay,
            'AvgConfrim' => (float)$avgConfrim,
            'AppealTime' => $appeal,
            'BuyList' => $buyList,
            'SellList' => $sellList
        ];

        return $data;
    }

    /**
     * @method 订单信息
     * @param int $id 订单Id
     */
    public function Info(int $id)
    {
        $order = DB::table('CTCOrder')->where('Id', $id)->first();
        if (empty($order)) throw new ArException(ArException::SELF_ERROR, '订单不存在');
        $coin = DB::table('Coin')->where('Id', $order->CoinId)->first();
        if (empty($coin)) throw new ArException(ArException::SELF_ERROR, '订单币种错误');
        $member = DB::table('Members')->where('Id', $order->MemberId)->first();
        if (empty($member)) throw new ArException(ArException::SELF_ERROR, '交易用户错误');
        //

        $memberInfo = $this->MemberInfo($id);

        $data = [
            'Id' => $order->Id,
            'Price' => $order->Price,
            'Fee' => $order->Type == 2 ? $order->Fee : $order->RecvFee,
            'IsBank' => $order->IsBank,
            'IsWechat' => $order->IsWechat,
            'SurplusNumber' => $order->SurplusNumber,
            'IsAlipay' => $order->IsAlipay,
            'CoinName' => $coin->EnName,
            'MinMoney' => $order->MinMoney,
            'MaxMoney' => $order->MaxMoney,
            'Name' => $member->NickName,
            'TradeNum' => $memberInfo['TradeNumber'],
            'SuccessRation' => $memberInfo['SuccessRatio'],
            'IsAuth' => $member->IsAuth,
            'AvgPayTime' => $memberInfo['AvgPayTime'],
            'AvgConfrim' => $memberInfo['AvgConfrim'],
            'AppealTime' => 0
        ];
        return $data;
    }

    /**
     * @method 取消订单
     * @param int $uid 用户
     * @param int $id 订单Id
     */
    public function Cancle(int $uid, int $id)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($id <= 0) throw new ArException(ArException::PARAM_ERROR);

        DB::beginTransaction();
        try {
            $trade = DB::table('CTCTrade')->where('Id', $id)->where('State', 0)->first();
            if (empty($trade)) throw new ArException(ArException::SELF_ERROR, '订单不存在或已取消');
            if ($trade->Type == 1 && $trade->MemberId != $uid)
                throw new ArException(ArException::SELF_ERROR, '订单不存在或已取消');

            if ($trade->Type == 2 && $trade->OrderMemberId != $uid)
                throw new ArException(ArException::SELF_ERROR, '订单不存在或已取消');

            $allow_cancel_time = SettingModel::getValueByKey('allow_cancel_time') ?? 0;
            if (($trade->AddTime + $allow_cancel_time) >= time())
                throw new ArException(ArException::SELF_ERROR, '规定时间不允许取消订单，请稍后再试');

            $order = DB::table('CTCOrder')->where('Id', $trade->OrderId)->first();
            if (empty($order)) throw new ArException(ArException::SELF_ERROR, '订单错误');
            $fee = bcmul($order->Fee, $trade->Number, 10);
            $number = bcadd($fee, $trade->Number, 10);
            $coin = Coin::where('Id', $trade->CoinId)->first();
            if ($trade->Type == 1) {
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
            DB::table('CTCTrade')->where('Id', $id)->where('State', 0)->update(['State' => 3, 'FinishTime' => time()]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

    /**
     * @method 确认收款
     * @param int $uid 用户
     * @param int $id 订单Id
     */
    public function Confirm(int $uid, int $id)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($id <= 0) throw new ArException(ArException::PARAM_ERROR);

        DB::beginTransaction();
        try {
            $trade = DB::table('CTCTrade')->where('Id', $id)->where('State', 1)->first();
            if (empty($trade)) throw new ArException(ArException::SELF_ERROR, '交易不存在');

            $order = DB::table('CTCOrder')->where('Id', $trade->OrderId)->first();
            if (empty($order)) throw new ArException(ArException::SELF_ERROR, '订单不存在');


            if ($trade->Type == 1 && $trade->OrderMemberId != $uid)
                throw new ArException(ArException::SELF_ERROR, '错误操作');

            if ($trade->Type == 2 && $trade->MemberId != $uid)
                throw new ArException(ArException::SELF_ERROR, '错误操作');

            $buyMember = 0;
            $sellMember = 0;
            if ($trade->Type == 1) {
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
            if (empty($setting)) throw new ArException(ArException::SELF_ERROR, '系统错误');

            $coin = DB::table('Coin')->where('Id', $trade->CoinId)->first();
            //扣除手续费
            $recvFee = bcmul($trade->Number, $recvFee, 10);
            $memberCoin = DB::table('MemberCoin')->where('CoinId', $trade->CoinId)->where('MemberId', $buyMember)->first();
            //把币拨给买家
            $coin = Coin::where('Id', $trade->CoinId)->first();
            if (empty($memberCoin)) {
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
            // $fee = bcmul($trade->Number, $fee, 10);//手续费
            $ret = DB::table('MemberCoin')
                ->where('MemberId', $sellMember)
                ->where('CoinId', $trade->CoinId)
                ->where('Forzen', '>=', bcadd($trade->Number, $fee, 10))
                ->decrement('Forzen', bcadd($trade->Number, $fee, 10));
            if (!$ret) throw new Exception('请勿重复提交');
            $ret = DB::table('CTCTrade')->where('Id', $id)->where('State', 1)->update([
                'State' => 2,
                'FinishTime' => time()
            ]);
            if (!$ret) throw new Exception('请勿重复提交');
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }


        try {
            $mid = 0;
            if ($trade->Type == 1) {
                $mid = $trade->MemberId;
            } else {
                $mid = $trade->OrderMemberId;
            }
            $member = DB::table('Members')->where('Id', $mid)->first();
            //$this->JuHeMsg($member->Phone, self::$_trade_confirm);
            if (isset($member->ClientId)) {
                $setting = DB::table('Setting')->whereRaw("k = 'app_id' or k = 'app_key' or k = 'master_secret'")->pluck('v', 'k')->all();
                $notify = new SendAppNotify(trim($setting['app_id']), trim($setting['app_key']), trim($setting['master_secret']));
                $notify->Push($member->ClientId, "订单已完成「{$trade->OrderSn}」", "您有一笔订单已完成，订单编号：{$trade->OrderSn}。赶快去查收把～");
            }

            DB::table('Notice')->insert([
                'MemberId' => $mid,
                'Title' => '订单已完成',
                'Content' => "您有一笔订单已完成，订单编号：{$trade->OrderSn}。赶快去查收把～",
                'AddTime' => time(),
                'IsDel' => 0,
                'IsRead' => 0,
                'Type' => 0,
            ]);
        } catch (\Exception $e) {
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }

        event(new UserTransactionAmountEvent($trade->OrderMemberId, $trade->Number));

        (new MemberService)->updateTxLevel($member->ParentId);
    }

    /**
     * @method 卖出
     * @param int $uid
     * @param int $id 广告单Id
     * @param $number 卖出数量
     */
    public function Sell($uid, $id, $IsAddress, $isWechat, $isAlipay, $isbank, $AuthCode)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($id <= 0) throw new ArException(ArException::PARAM_ERROR);
        // if(!is_numeric($number) || bccomp($number, 0, 10) <= 0)
        //     throw new ArException(ArException::SELF_ERROR,'出售数量有误');

        if (!in_array($IsAddress, [0, 1])) throw new ArException(ArException::SELF_ERROR, '付款方式错误');
        if (!in_array($isbank, [0, 1])) throw new ArException(ArException::SELF_ERROR, '付款方式错误');
        if (!in_array($isWechat, [0, 1])) throw new ArException(ArException::SELF_ERROR, '付款方式错误');
        if (!in_array($isAlipay, [0, 1])) throw new ArException(ArException::SELF_ERROR, '付款方式错误');

        $PayMethod = 0;
        if ($isAlipay == 1) $PayMethod = 2;
        if ($isWechat == 1) $PayMethod = 3;
        if ($IsAddress == 1) $PayMethod = 4;
        if ($isbank == 1) $PayMethod = 1;
        if (($IsAddress + $isWechat + $isAlipay + $isbank) != 1)
            throw new ArException(ArException::SELF_ERROR, '请选择一种支付方式');

        $member = DB::table('Members')->where('Id', $uid)->where('IsAuth', 1)->first();
        if (empty($member)) throw new ArException(ArException::SELF_ERROR, '请先实名认证');

        $setting = DB::table('CTCSetting')->first();
        $white_list = SettingModel::getValueByKey('white_list');//白名单不受限制
        if (!in_array($uid, explode(',', $white_list))) {
            if ($member->IsFrozenCTC != 0) throw new ArException(ArException::SELF_ERROR, '此账号已被冻结使用CTC功能');
            if ($member->IsFrozenCTCSell != 0) throw new ArException(ArException::SELF_ERROR, '您还未满足出售条件');

            $count = DB::table('CTCTrade')->where('MemberId', $uid)->whereNotIn('State', [2, 3, 4])->count();
            if ($count >= $setting->MaxTrade)
                throw new ArException(ArException::SELF_ERROR, '个人订单数量达到上限');
        }

        $member = DB::table('Members')->where('Id', $uid)->first();
        $auth = Redis::hget('vcode', $member->Phone);
        if (empty($auth)) throw new ArException(ArException::SELF_ERROR, '请先发送验证码');
        $auth = json_decode($auth, true);
        if (!is_array($auth)) throw new ArException(ArException::SELF_ERROR, '验证码已失效，请重新发送');
        if ($auth['Code'] != $AuthCode) throw new ArException(ArException::SELF_ERROR, '验证码错误');
        if ($auth['ExpireTime'] < time()) throw new ArException(ArException::SELF_ERROR, '验证码已过期');

        DB::beginTransaction();
        try {
            if ($IsAddress == 1 && $member->IsBindAddress != 1)
                throw new ArException(ArException::SELF_ERROR, '请先绑定USDT地址');

            if ($isbank == 1 && $member->IsBindBank != 1)
                throw new ArException(ArException::SELF_ERROR, '请先绑定银行卡');

            if ($isWechat == 1 && $member->IsBindWx != 1)
                throw new ArException(ArException::SELF_ERROR, '请先绑定微信');

            if ($isAlipay == 1 && $member->IsBindAlipay != 1)
                throw new ArException(ArException::SELF_ERROR, '请先绑定支付宝');

            $order = DB::table('CTCOrder')->where('Id', $id)->where('Type', 2)->where('State', 0)->first();
            if (empty($order)) throw new ArException(ArException::SELF_ERROR, '订单不存在');

            $number = $order->Number;//一次性匹配所有
            if ($order->MemberId == $uid)
                throw new ArException(ArException::SELF_ERROR, '不能操作自己的订单');

            $sumPrice = bcmul($number, $order->Price, 10);
            // if(bccomp($sumPrice, $order->MinMoney, 10) < 0)
            //     throw new ArException(ArException::SELF_ERROR,'金额不能低于'.$order->MinMoney);

            // if(bccomp($sumPrice, $order->MaxMoney, 10) > 0)
            //     throw new ArException(ArException::SELF_ERROR,'金额不能高于'.$order->MaxMoney);

            // if(!in_array($uid, explode(',', $white_list))){
            //     if(bccomp($number, $order->SurplusNumber, 10) > 0)
            //     throw new ArException(ArException::SELF_ERROR,'订单数量不足');
            // }

            //如果设置了个人手续费 则已个人手续费为准
            if ($member->IsOpenFee == 1) {
                $fee = $member->Fee;
            } else {
                $fee = $order->Fee;
            }
            //冻结数量
            $feeNumber = bcmul($fee, $number, 10);

            DB::table('Setting')->where('k', 'ctc_tx_fee')->increment('v', $feeNumber);//累计交易手续费

            $frozen = bcadd($feeNumber, $number, 10);
            $memberCoin = DB::table('MemberCoin')
                ->where('MemberId', $uid)
                ->where('CoinId', $order->CoinId)
                ->where('Money', '>', $frozen)
                ->first();
            if (empty($memberCoin)) throw new ArException(ArException::SELF_ERROR, '币种余额不足');

            DB::table('MemberCoin')->where('MemberId', $uid)->where('CoinId', $order->CoinId)->update([
                'Money' => DB::raw("Money-{$frozen}"),
                'Forzen' => DB::raw("Forzen+{$frozen}")
            ]);
            $coin = Coin::where('Id', $order->CoinId)->first();
            self::AddLog($uid, -$frozen, $coin, 'ctc_sell');

            $sn = md5(uniqid(microtime(true)));
            $oid = DB::table('CTCTrade')->insertGetId([
                'OrderId' => $id,
                'Type' => 2,
                'MemberId' => $uid,
                'OrderMemberId' => $order->MemberId,
                'CoinId' => $order->CoinId,
                'Number' => $number,
                'Price' => $order->Price,
                'SumPrice' => bcmul($order->Price, $number, 10),
                'OrderSn' => $sn,
                'RemarkCode' => mt_rand(100000, 999999),
                'AddTime' => time(),
                'IsAddress' => $IsAddress,
                'IsWechat' => $isWechat,
                'IsAlipay' => $isAlipay,
                'IsBank' => $isbank,
                'Fee' => $feeNumber,
                'PayMethod' => $PayMethod,
            ]);
            $orderSn = date('Ymd') . $oid;
            DB::table('CTCTrade')->where('Id', $oid)->update(['OrderSn' => $orderSn]);
            DB::table('CTCOrder')->where('Id', $id)->decrement('SurplusNumber', $number);
            //如果剩余数量不足最低限额 订单终止
            $order = DB::table('CTCOrder')->where('Id', $id)->first();
            $surplusMoney = bcmul($order->SurplusNumber, $order->Price, 10);
            // if(bccomp($surplusMoney, $order->MinMoney, 10) < 0){
            DB::table('CTCOrder')->where('Id', $id)->update([
                'State' => 1
            ]);
            // }

            DB::table('CTCOrder')->where('Id', $id)->where('SurplusNumber', 0)->update([
                'State' => 2
            ]);

            $has = DB::table('CTCOrder')->where('Id', $id)->where('SurplusNumber', '<', 0)->first();
            if (!empty($has)) throw new ArException(ArException::SELF_ERROR, '剩余数量不足');

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
        try {
            $member = DB::table('Members')->where('Id', $order->MemberId)->first();
            $this->smsTreasureMsg($member->Phone);
            if (isset($member->ClientId)) {
                $setting = DB::table('Setting')->whereRaw("k = 'app_id' or k = 'app_key' or k = 'master_secret'")->pluck('v', 'k')->all();
                $notify = new SendAppNotify(trim($setting['app_id']), trim($setting['app_key']), trim($setting['master_secret']));
                $notify->Push($member->ClientId, "待付款订单「{$orderSn}」", "您有一笔待付款订单，订单编号：{$orderSn}。赶快去处理把～");
            }

            DB::table('Notice')->insert([
                'MemberId' => $order->MemberId,
                'Title' => '待付款订单',
                'Content' => "您有一笔待付款订单，订单编号：{$orderSn}。赶快去处理把～",
                'AddTime' => time(),
                'IsDel' => 0,
                'IsRead' => 0,
                'Type' => 0,
            ]);
        } catch (\Exception $e) {
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
        return $oid;
    }

    /**
     * @method 付款
     * @param int $uid 用户Id
     * @param int $id 交易订单Id
     * @param int $method 支付方式 1银行卡 2支付宝 3微信
     */
    public function TradePay(int $uid, int $id, $imgs)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($id <= 0) throw new ArException(ArException::PARAM_ERROR);
        // if(!in_array($method, [1,2,3,4])) throw new ArException(ArException::SELF_ERROR,'支付方式错误');
        if (!is_array(json_decode($imgs, true))) throw new ArException(ArException::SELF_ERROR, '图片错误');

        DB::beginTransaction();
        try {
            $trade = DB::table('CTCTrade')->where('Id', $id)->where('State', 0)->first();
            if (empty($trade)) throw new ArException(ArException::SELF_ERROR, '交易不存在');
            if ($trade->Type == 1 && $trade->MemberId != $uid)
                throw new ArException(ArException::SELF_ERROR, '错误操作');

            if ($trade->Type == 2 && $trade->OrderMemberId != $uid)
                throw new ArException(ArException::SELF_ERROR, '错误操作');

            // if($method == 1 && $trade->IsBank != 1)
            //     throw new ArException(ArException::SELF_ERROR,'此交易不支持银行卡支付');

            // if($method == 2 && $trade->IsAlipay != 1)
            //     throw new ArException(ArException::SELF_ERROR,'此交易不支持支付宝支付');

            // if($method == 3 && $trade->IsWechat != 1)
            //     throw new ArException(ArException::SELF_ERROR,'此交易不支持微信支付');

            // if($method == 4 && $trade->IsAddress != 1)
            //     throw new ArException(ArException::SELF_ERROR,'此交易不支持USDT地址');

            DB::table('CTCTrade')->where('Id', $id)->update([
                'State' => 1,
                'PayTime' => time(),
                // 'PayMethod' => $method,
                'Imgs' => $imgs
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }

        try {
            $mid = 0;
            if ($trade->Type == 1) {
                $mid = $trade->OrderMemberId;
            } else {
                $mid = $trade->MemberId;
            }
            $member = DB::table('Members')->where('Id', $mid)->first();
            //$this->JuHeMsg($member->Phone, self::$_trade_pay);
            if (isset($member->ClientId)) {
                $setting = DB::table('Setting')->whereRaw("k = 'app_id' or k = 'app_key' or k = 'master_secret'")->pluck('v', 'k')->all();
                $notify = new SendAppNotify(trim($setting['app_id']), trim($setting['app_key']), trim($setting['master_secret']));
                $notify->Push($member->ClientId, "待确认订单「{$trade->OrderSn}」", "您有一笔待确认订单，订单编号：{$trade->OrderSn}。赶快去处理把～");
            }

            DB::table('Notice')->insert([
                'MemberId' => $mid,
                'Title' => '待确认订单',
                'Content' => "您有一笔待确认订单，订单编号：{$trade->OrderSn}。赶快去处理把～",
                'AddTime' => time(),
                'IsDel' => 0,
                'IsRead' => 0,
                'Type' => 0,
            ]);
        } catch (\Exception $e) {
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }

    }

    /**
     * @method 订单详情
     */
    public function TradeDetail($uid, $id)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($id <= 0) throw new ArException(ArException::PARAM_ERROR);

        $setting = DB::table('CTCSetting')->first();
        if (empty($setting)) throw new ArException(ArException::SELF_ERROR, '系统设置错误');

        $trade = DB::table('CTCTrade')->where('Id', $id)->where(function ($query) use ($uid) {
            $query->where('MemberId', $uid)->orWhere('OrderMemberId', $uid);
        })->first();
        if (empty($trade)) throw new ArException(ArException::SELF_ERROR, '你没有此订单');

        $coin = DB::table('Coin')->where('Id', $trade->CoinId)->first();
        if (empty($coin)) throw new ArException(ArException::SELF_ERROR, '订单币种错误');

        if (empty($trade)) throw new ArException(ArException::SELF_ERROR, '你没有此订单');
        $member = DB::table('Members')->where('Id', $trade->MemberId)->first();
        $orderMember = DB::table('Members')->where('Id', $trade->OrderMemberId)->first();
        //
        $operate = 1;
        if ($trade->Type == 1 && $trade->MemberId != $uid) $operate = 2;
        else if ($trade->Type == 2 && $trade->OrderMemberId != $uid) $operate = 2;

        if ($trade->Type == 1) $sellMemberId = $trade->OrderMemberId;
        else $sellMemberId = $trade->MemberId;

        // $bank = DB::table('MemberBindPay')->where('MemberId', $sellMemberId)->where('Type', 1)
        //     ->select('Name','CardNo','Bank')->first();

        $wechat = DB::table('BindPay')->where('MemberId', $sellMemberId)->where('PayType', 1)
            ->select('NickName', 'QrCode', 'Account')->first();

        if (!empty($wechat)) $wechat->QrCode = $this->QiniuDomain() . $wechat->QrCode;

        $alipay = DB::table('BindPay')->where('MemberId', $sellMemberId)->where('PayType', 2)
            ->select('NickName', 'QrCode', 'Account')->first();

        if (!empty($alipay)) $alipay->QrCode = $this->QiniuDomain() . $alipay->QrCode;

        $address = DB::table('BindPay')->where('MemberId', $sellMemberId)->where('PayType', 3)
            ->select('Account')->first();

        $imgs = (array)json_decode($trade->Imgs, true);
        $images = [];
        $domain = $this->QiniuDomain();
        foreach ($imgs as $item) {
            $images[] = $domain . $item;
        }

        $Appeal = DB::table('CTCAppeal')->where('TradeId', $id)->first();
        if ($Appeal) {
            $Appeal->Reason = DB::table('CTCAppealReason')->where('Id', $Appeal->ReasonId)->value('Content');
        }

        $data = [
            'Id' => $trade->Id,
            'RemarkCode' => $trade->RemarkCode,
            'CancleTime' => $trade->AddTime + $setting->CancleTime - time(),
            'CoinName' => $coin->EnName,
            'Number' => $trade->Number,
            'Price' => $trade->Price,
            'SumPrice' => bcmul($trade->Price, $trade->Number, 10),
            'IsAddress' => $trade->IsAddress,
            'PayMethod' => $trade->PayMethod,
            'IsWechat' => $trade->IsWechat,
            'IsAlipay' => $trade->IsAlipay,
            'Type' => $trade->Type,
            'State' => $trade->State,
            'SellMember' => $trade->Type == 1 ? $orderMember->NickName : $member->NickName,
            'BuyMember' => $trade->Type == 1 ? $member->NickName : $orderMember->NickName,
            'OrderSn' => $trade->OrderSn,
            'AddTime' => $trade->AddTime,
            'PayTime' => $trade->PayTime,
            // 'Bank' => (array) $bank,
            'Wechat' => (array)$wechat,
            'Alipay' => (array)$alipay,
            'Address' => (array)$address,
            'Images' => $images,
            'Operate' => $operate,
            'Appeal' => (array)$Appeal,
            'BuyMemberPhone' => $orderMember->Phone
        ];
        return $data;
    }

    /**
     * @method
     * @param int $uid 用户Id
     * @param int $id 广告单id
     */
    public function TradeMyList($uid, $order_type, $order_status, $min, $max, $start, $end, $count)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($count <= 0) throw new ArException(ArException::PARAM_ERROR);

        $table = DB::table('CTCTrade');
        if ($min > 0 && $max > 0 && $min >= $max) throw new ArException(ArException::SELF_ERROR, '请正确输入交易数量');
        if ($start > 0 && $end > 0 && $start >= $end) throw new ArException(ArException::SELF_ERROR, '请正确选择交易时间');
        if ($min > 0) $table->where('Number', '>=', $min);
        if ($max > 0) $table->where('Number', '<=', $max);
        if ($start > 0) $table->where('AddTime', '>=', $start);
        if ($end > 0) $table->where('AddTime', '<=', $end);
        switch ($order_type) {
            case 1:
                $table->where('OrderMemberId', $uid);
                break;
            case 2:
                $table->where('MemberId', $uid);
                break;
            default:
                $table->whereRaw("(OrderMemberId = $uid or MemberId = $uid)");
                break;
        }
        switch ($order_status) {
            case 1:
                $table->where('State', 0);
                break;
            case 2:
                $table->where('State', 1);
                break;
            case 3:
                $table->where('State', 2);
                break;
            case 4:
                $table->where("(State = 3 or State = 4)");
                break;
            case 5:
                $table->where('State', 5);
                break;
        }

        $data = $table->orderBy('Id', 'desc')->paginate($count);
        $list = [];
        foreach ($data as $item) {
            $coin = DB::table('Coin')->where('Id', $item->CoinId)->first();
            if (empty($coin)) continue;
            $order = DB::table('CTCOrder')->where('Id', $item->OrderId)->first();
            if (empty($order)) continue;
            $list[] = [
                'Id' => $item->Id,
                'Type' => $item->Type,
                'CoinName' => $coin->EnName,
                'RemarkCode' => $item->RemarkCode,
                'State' => $item->State,
                'AddTime' => $item->AddTime,
                'Number' => $item->Number,
                'SumPrice' => bcmul($item->Price, $item->Number, 10)
            ];
        }
        return ['list' => $list, 'total' => $data->total()];
    }

    /**
     * @method 订单列表
     * @param int $uid 用户Id
     * @param int $type 订单类型 0全部 1购买 2出售
     */
    public function TradeList(int $uid, int $type, int $count)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($count <= 0) throw new ArException(ArException::PARAM_ERROR);

        $data = DB::table('CTCTrade')->where('MemberId', $uid);
        if ($type > 0) $data = $data->where('Type', $type);
        $data = $data->orderBy('Id', 'desc')->paginate($count);
        $list = [];
        foreach ($data as $item) {
            $coin = DB::table('Coin')->where('Id', $item->CoinId)->first();
            if (empty($coin)) continue;
            $order = DB::table('CTCOrder')->where('Id', $item->OrderId)->first();
            if (empty($order)) continue;
            $list[] = [
                'Id' => $item->Id,
                'Type' => $item->Type,
                'CoinName' => $coin->EnName,
                'RemarkCode' => $item->RemarkCode,
                'State' => $item->State,
                'AddTime' => $item->AddTime,
                'Number' => $item->Number,
                'SumPrice' => bcmul($item->Price, $item->Number, 10)
            ];
        }
        return ['list' => $list, 'total' => $data->total()];
    }

    /**
     * @method 购买
     */
    public function Buy(int $uid, int $id, $number)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($id <= 0) throw new ArException(ArException::PARAM_ERROR);
        if (!is_numeric($number) || bccomp($number, 0, 10) <= 0)
            throw new ArException(ArException::SELF_ERROR, '购买数量错误');

        $member = DB::table('Members')
            ->where('Id', $uid)
            // ->where('IsAuth', 1)
            ->first();
        if (empty($member)) throw new ArException(ArException::SELF_ERROR, '请先实名认证');

        if ($member->IsFrozenCTC != 0) throw new ArException(ArException::SELF_ERROR, '此账号已被冻结使用CTC功能');

        DB::beginTransaction();
        try {
            $order = DB::table('CTCOrder')->where('Id', $id)->where('Type', 1)->where('State', 0)->first();
            if (empty($order)) throw new ArException(ArException::SELF_ERROR, '订单不存在');

            if ($order->MemberId == $uid)
                throw new ArException(ArException::SELF_ERROR, '不能操作自己的订单');

            $sumPrice = bcmul($number, $order->Price, 10);
            if (bccomp($sumPrice, $order->MinMoney, 10) < 0)
                throw new ArException(ArException::SELF_ERROR, '金额不能低于' . $order->MinMoney);

            if (bccomp($sumPrice, $order->MaxMoney, 10) > 0)
                throw new ArException(ArException::SELF_ERROR, '金额不能高于' . $order->MaxMoney);

            if (bccomp($number, $order->SurplusNumber, 10) > 0)
                throw new ArException(ArException::SELF_ERROR, '订单数量不足');

            if ($member->IsOpenFee == 1) {
                $recvFee = $member->RecvFee;
            } else {
                $recvFee = $order->RecvFee;
            }

            $oid = DB::table('CTCTrade')->insertGetId([
                'OrderId' => $id,
                'Type' => 1,
                'MemberId' => $uid,
                'OrderMemberId' => $order->MemberId,
                'CoinId' => $order->CoinId,
                'Number' => $number,
                'Price' => $order->Price,
                'SumPrice' => bcmul($order->Price, $number, 10),
                'OrderSn' => md5(uniqid(microtime(true))),
                'RemarkCode' => mt_rand(100000, 999999),
                'AddTime' => time(),
                'IsBank' => $order->IsBank,
                'IsAddress' => $order->IsAddress,
                'IsWechat' => $order->IsWechat,
                'IsAlipay' => $order->IsAlipay,
                'RecvFee' => $recvFee,
                'Fee' => $order->Fee
            ]);
            $orderSn = date('Ymd') . $oid;
            DB::table('CTCTrade')->where('Id', $oid)->update(['OrderSn' => $orderSn]);

            DB::table('CTCOrder')->where('Id', $id)->decrement('SurplusNumber', $number);

            //如果剩余数量不足最低限额 则退回 并且订单终止
            $order = DB::table('CTCOrder')->where('Id', $id)->first();
            $surplusMoney = bcmul($order->SurplusNumber, $order->Price, 10);
            if (bccomp($surplusMoney, $order->MinMoney, 10) < 0) {
                $coin = Coin::where('Id', $order->CoinId)->first();
                $fee = bcmul($order->SurplusNumber, $order->Fee, 10);
                $number = bcadd($order->SurplusNumber, $fee, 10);
                DB::table('MemberCoin')->where('MemberId', $order->MemberId)->where('CoinId', $order->CoinId)->update([
                    'Forzen' => DB::raw("Forzen-{$number}"),
                    'Money' => DB::raw("Money+{$number}")
                ]);
                if (bccomp($number, 0, 10) > 0) self::AddLog($order->MemberId, $number, $coin, 'ctc_sell_cancle');

                DB::table('CTCOrder')->where('Id', $id)->update([
                    'State' => 1
                ]);
            }

            DB::table('CTCOrder')->where('Id', $id)->where('SurplusNumber', 0)->update([
                'State' => 2
            ]);

            $has = DB::table('CTCOrder')->where('Id', $id)->where('SurplusNumber', '<', 0)->first();
            if (!empty($has)) throw new ArException(ArException::SELF_ERROR, '剩余数量不足');

            $setting = DB::table('CTCSetting')->first();
            $count = DB::table('CTCTrade')->where('MemberId', $uid)->whereNotIn('State', [2, 3, 4])->count();
            if ($count > $setting->MaxTrade)
                throw new ArException(ArException::SELF_ERROR, '个人订单数量达到上限');

            DB::commit();
            return $oid;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

    /**
     * @method 终止
     */
    public function OrderStop(int $uid, int $id)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($id <= 0) throw new ArException(ArException::PARAM_ERROR);

        DB::beginTransaction();
        try {
            $order = DB::table('CTCOrder')->where('Id', $id)->where('MemberId', $uid)->where('State', 0)->first();
            if (empty($order)) throw new ArException(ArException::SELF_ERROR, '订单不存在或已终止');
            DB::table('CTCOrder')->where('Id', $id)->update(['State' => 1]);
            $coin = Coin::where('Id', $order->CoinId)->first();
            if ($order->Type == 1) {
                //出售订单返回剩余数量和一部分手续费
                $fee = bcmul($order->Fee, $order->SurplusNumber, 10);
                $number = bcadd($fee, $order->SurplusNumber, 10);
                DB::table('MemberCoin')->where('MemberId', $uid)->where('CoinId', $order->CoinId)->update([
                    'Forzen' => DB::raw("Forzen-{$number}"),
                    'Money' => DB::raw("Money+{$number}")
                ]);
                self::AddLog($uid, $number, $coin, 'ctc_close_order');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

    /**
     * @method 我的发布
     */
    public function MyList(int $uid, int $tpye, int $count)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($count <= 0) throw new ArException(ArException::PARAM_ERROR);

        $data = DB::table('CTCOrder')->where('MemberId', $uid)->where('State', 0)->where('Type', $tpye)->orderBy('Id', 'desc')->paginate($count);
        $list = [];
        $tx_price = SettingModel::getValueByKey('tx_price');
        foreach ($data as $item) {
            $coin = DB::table('Coin')->where('Id', $item->CoinId)->first();
            if (empty($coin)) continue;
            $list[] = [
                'Id' => $item->Id,
                'AddTime' => $item->AddTime,
                'Number' => $item->Number,
                'CoinName' => $coin->EnName,
                'SumPrice' => bcmul($item->Number, $item->Price, 4),
                'CompleteNumber' => bcsub($item->Number, $item->SurplusNumber, 10),
                'State' => $item->State,
                'Rate' => bcdiv(bcsub($item->Number, $item->SurplusNumber, 10), $item->Number, 4),
                'Price_USDT' => bcmul($tx_price, $item->Number, 10),
                'Complete_USDT' => bcmul($tx_price, bcsub($item->Number, $item->SurplusNumber, 10), 10),
                'Surplus_USDT' => bcmul($tx_price, $item->SurplusNumber, 10),
                'wait_deal' => DB::table('CTCTrade')->where('OrderId', $item->Id)->where('State', 1)->count()
            ];
        }
        return ['list' => $list, 'total' => $data->total()];
    }

    /**
     * @method 交易列表
     */
    public function List(int $type, int $count, int $id, string $filter = '')
    {
        if ($count <= 0) throw new ArException(ArException::PARAM_ERROR);
        $data = DB::table('CTCOrder')->where('State', 0)->where('Type', $type);//求购单
        if ($id > 0) $data = $data->where('CoinId', $id);

        //筛选
        $ctc_filter = SettingModel::getValueByKey('ctc_filter');
        if ($ctc_filter) {
            if (in_array($filter, explode(',', $ctc_filter))) {
                $filter_data = explode('-', $filter);
                if (count($filter_data) == 2) {
                    $data->where('SurplusNumber', '>=', min($filter_data));
                    $data->where('SurplusNumber', '<=', max($filter_data));
                } else {
                    $data->where('SurplusNumber', '>=', $filter_data);
                }
            }
        }

        $data = $data->orderBy('Id', 'desc')->paginate($count);
        $toatl = $data->total();
        $list = [];
        foreach ($data as $item) {
            $member = DB::table('Members')->where('Id', $item->MemberId)->first();
            if (empty($member)) {
                $toatl--;
                continue;
            }
            $coin = DB::table('Coin')->where('Id', $item->CoinId)->first();
            if (empty($coin)) {
                $toatl--;
                continue;
            }
            $volumeRation = 0;
            $volumeNum = DB::table('CTCTrade')->where(function ($query) use ($item) {
                $query->where('OrderMemberId', $item->MemberId)
                    ->orWhere('MemberId', $item->MemberId);
            })->where('State', 2)->count();
            $tradeNum = DB::table('CTCTrade')->where('MemberId', $item->MemberId)->orWhere('OrderMemberId', $item->MemberId)->count();
            if ($tradeNum > 0) $volumeRation = bcdiv($volumeNum, $tradeNum, 4);
            $list[] = [
                'Id' => $item->Id,
                'Avatar' => $member->Avatar ? $this->QiniuDomain() . $member->Avatar : '',
                'Phone' => substr_replace($member->Phone, '****', 3, 4),
                'Name' => $member->NickName,
                'Level' => max($member->Level, $member->SettingLevel),
                'Volume' => $volumeNum,
                'VolumeRatio' => $volumeRation,
                'Number' => $item->SurplusNumber,
                'MinMoney' => $item->MinMoney,
                'MaxMoney' => $item->MaxMoney,
                'Price' => $item->Price,
                'CoinName' => $coin->EnName,
                'CoinId' => $item->CoinId,
                'IsBank' => $item->IsBank,
                'IsWechat' => $item->IsWechat,
                'IsAlipay' => $item->IsAlipay,
                'IsAddress' => $item->IsAddress,
            ];
        }

        $price = SettingModel::getValueByKey('tx_price') ?? 0;
        $buy_amount = DB::table('CTCOrder')->where('State', 0)->sum('Number');
        $tx_amount = DB::table('CTCTrade')->where('State', 2)->sum('Number');
        return ['price' => $price, 'buy_amount' => $buy_amount, 'tx_amount' => $tx_amount, 'list' => $list, 'total' => $data->total()];
    }

    /**
     * @method 获取交易币种
     * @param int $tpye 1出售 2求购
     */
    public function Coin(int $tpye)
    {
        if ($tpye == 1) {
            $data = DB::table('CTCCoinSetting')->where('IsSell', 1)->get();
        } else {
            $data = DB::table('CTCCoinSetting')->where('IsBuy', 1)->get();
        }
        $list = [];
        foreach ($data as $item) {
            $coin = DB::table('Coin')->where('Id', $item->CoinId)->first();
            $list[] = [
                'CoinId' => $item->CoinId,
                'CoinName' => $coin->EnName
            ];
        }
        return $list;
    }

    /**
     * @method 发布单
     */
    public function AddSellOrder(int $uid, int $coinId, $number, $price, int $isAddress, int $isWechat, int $isAlipay, int $isBank, int $type)
    {
        if ($coinId <= 0) throw new ArException(ArException::PARAM_ERROR);
        if (!is_numeric($number) || bccomp($number, 0, 8) <= 0) throw new ArException(ArException::SELF_ERROR, '出售数量错误');
        if (!is_numeric($price) || bccomp($price, 0, 8) <= 0) throw new ArException(ArException::SELF_ERROR, '价格错误');
        // if(!is_numeric($minMoney)) throw new ArException(ArException::SELF_ERROR,'最低限额错误');
        // if(!is_numeric($maxMoney)) throw new ArException(ArException::SELF_ERROR,'最高限额错误');
        if (!in_array($isAddress, [0, 1])) throw new ArException(ArException::SELF_ERROR, '支付方式错误');
        if (!in_array($isBank, [0, 1])) throw new ArException(ArException::SELF_ERROR, '支付方式错误');
        if (!in_array($isWechat, [0, 1])) throw new ArException(ArException::SELF_ERROR, '支付方式错误');
        if (!in_array($isAlipay, [0, 1])) throw new ArException(ArException::SELF_ERROR, '支付方式错误');
        if (!in_array($type, [1, 2])) throw new ArException(ArException::SELF_ERROR, '订单类型错误');
        $setting = DB::table('CTCSetting')->first();
        if ($number > $setting->MaxMoney) throw new ArException(ArException::SELF_ERROR, '求购数量不能超过 ' . $setting->MaxMoney);
        if ($number < $setting->MinMoney) throw new ArException(ArException::SELF_ERROR, '求购数量不能小于 ' . $setting->MinMoney);
        if ($number != (int)$number) throw new ArException(ArException::SELF_ERROR, '求购数量必须是整数');

        if (($isAddress + $isWechat + $isAlipay + $isBank) == 0) throw new ArException(ArException::SELF_ERROR, '请选择支付方式');


        if (empty($setting)) throw new ArException(ArException::SELF_ERROR, '平台设置错误');
        if (date('H') < $setting->start_time || date('H') > $setting->end_time)
            throw new ArException(ArException::SELF_ERROR, "未到开放时间（每日开放时间{$setting->start_time}点-{$setting->end_time}点）");

        // if(bccomp($minMoney, $setting->MinMoney, 10) < 0)
        //     throw new ArException(ArException::SELF_ERROR,'最低限额不能低于'.$setting->MinMoney);

        // if(bccomp($maxMoney, $setting->MaxMoney, 10) > 0)
        //     throw new ArException(ArException::SELF_ERROR,'最高限额不能高于'.$setting->MaxMoney);

        // if(bccomp($maxMoney, $minMoney, 10) <= 0)
        //     throw new ArException(ArException::SELF_ERROR,'最高限额不能低于最低限额');

        //$sumMoney = bcmul($number, $price, 10);
        //if (bccomp($sumMoney, $setting->MaxMoney, 10) > 0)
        //throw new ArException(ArException::SELF_ERROR, '订单总金额不能高于' . $setting->MaxMoney);

        //if (bccomp($sumMoney, $setting->MinMoney, 10) < 0)
        //throw new ArException(ArException::SELF_ERROR, '订单总金额不能低于' . $setting->MinMoney);

        if ($type == 1) {
            $sellCoin = DB::table('CTCCoinSetting')->where('CoinId', $coinId)->where('IsSell', 1)->first();
            if (empty($sellCoin)) throw new ArException(ArException::SELF_ERROR, '此币种不允许出售');
        } else {
            $sellCoin = DB::table('CTCCoinSetting')->where('CoinId', $coinId)->where('IsBuy', 1)->first();
            if (empty($sellCoin)) throw new ArException(ArException::SELF_ERROR, '此币种不允许求购');
        }

        $sumPrice = bcmul($number, $price, 10);
        // if(bccomp($sumPrice, $minMoney, 10) < 0)
        //     throw new ArException(ArException::SELF_ERROR,'总金额不能低于最低限额');

        $member = DB::table('Members')->where('Id', $uid)->first();
        if ($member->IsAuth != 1) throw new ArException(ArException::SELF_ERROR, '请先实名认证');

        if ($member->IsFrozenCTC != 0)
            throw new ArException(ArException::SELF_ERROR, '此账号已被禁止发布订单');

        // if($isAddress == 1 && $member->IsBindAddress != 1)
        //     throw new ArException(ArException::SELF_ERROR,'请先绑定USDT地址');

        // if($isBank == 1 && $member->IsBindBank != 1)
        //     throw new ArException(ArException::SELF_ERROR,'请先绑定银行卡');

        // if($isWechat == 1 && $member->IsBindWx != 1)
        //     throw new ArException(ArException::SELF_ERROR,'请先绑定微信');

        // if($isAlipay == 1 && $member->IsBindAlipay != 1)
        //     throw new ArException(ArException::SELF_ERROR,'请先绑定支付宝');

        $white_list = SettingModel::getValueByKey('white_list');//白名单不受限制
        if (!in_array($uid, explode(',', $white_list))) {
            $count = DB::table('CTCOrder')->where('MemberId', $uid)->where('State', 0)->count();
            if ($count >= $setting->MaxSellOrder)
                throw new ArException(ArException::SELF_ERROR, '个人订单已达到上限');
        }

        DB::beginTransaction();
        try {
            if ($type == 1) {
                $memberCoin = DB::table('MemberCoin')->where('MemberId', $uid)->where('CoinId', $coinId)->first();
                if (empty($memberCoin)) throw new ArException(ArException::COIN_NOT_ENOUGH);
                //如果设置了个人手续费 则已个人手续费为准
                $recvFee = $setting->RecvFee;
                if ($member->IsOpenFee == 1) {
                    $fee = $member->Fee;
                } else {
                    $fee = $setting->SellFee;
                }
            } else {
                $fee = $setting->SellFee;
                if ($member->IsOpenFee == 1) {
                    $recvFee = $member->RecvFee;
                } else {
                    $recvFee = $setting->RecvFee;
                }
            }

            DB::table('CTCOrder')->insert([
                'MemberId' => $uid,
                'CoinId' => $coinId,
                'Type' => $type,
                'Number' => $number,
                'Price' => $price,
                'MinMoney' => 0,
                'MaxMoney' => 0,
                'SurplusNumber' => $number,
                'IsBank' => $isBank,
                'IsAddress' => $isAddress,
                'IsWechat' => $isWechat,
                'IsAlipay' => $isAlipay,
                'Fee' => $fee,
                'RecvFee' => $recvFee,
                'AddTime' => time()
            ]);
            if ($type == 1) {
                $coin = Coin::where('Id', $coinId)->first();
                //冻结
                $fee = bcmul($number, $fee, 10);
                $frozen = bcadd($fee, $number, 10);
                DB::table('MemberCoin')->where('MemberId', $uid)->where('CoinId', $coinId)->update([
                    'Money' => DB::raw("Money-{$frozen}"),
                    'Forzen' => DB::raw("Forzen+{$frozen}")
                ]);
                self::AddLog($uid, -$frozen, $coin, 'ctc_order_sell');
            }
            //
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }

    }

    //申诉原因
    public function AppealReason()
    {
        $list = DB::table('CTCAppealReason')->get();
        return $list;
    }

    public function unfrozen(int $uid)
    {
        $user = DB::table('Members')->where('Id', $uid)->first();
        if (!$user) throw new ArException(ArException::SELF_ERROR, '该账号不存在');

        DB::beginTransaction();
        try {

            $result = DB::table('Members')->where('Id', $uid)->where('IsFrozenCTC', 1)->update([
                'IsFrozenCTC' => 0
            ]);
            if (!$result) throw new ArException(ArException::SELF_ERROR, '您无需解冻账号');

            //扣钱
            $unfrozen_amount = SettingModel::getValueByKey('unfrozen_amount') ?? 0;
            $unfrozen_coin = SettingModel::getValueByKey('unfrozen_coin') ?? 0;
            if ($unfrozen_amount > 0 && $unfrozen_coin > 0) {
                $result = DB::table('MemberCoin')
                    ->where('MemberId', $uid)
                    ->where('CoinId', $unfrozen_coin)
                    ->where('Money', '>=', $unfrozen_amount)
                    ->update([
                        'Money' => DB::raw("Money-{$unfrozen_amount}"),
                    ]);
                if (!$result) throw new ArException(ArException::SELF_ERROR, '余额不足');
                self::AddLog($uid, -$unfrozen_amount, Coin::find($unfrozen_coin), 'ctc_unfrozen');
            }

            DB::commit();
        } catch (ArException $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }

        return true;
    }

    public function remain($uid, $id)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($id <= 0) throw new ArException(ArException::PARAM_ERROR);

        $trade = DB::table('CTCTrade')->where('Id', $id)->where('OrderMemberId', $uid)->first();
        if (empty($trade)) throw new ArException(ArException::SELF_ERROR, '非法请求');

        $member = DB::table('Members')->where('Id', $trade->MemberId)->first();
        if (isset($member->ClientId)) {
            $setting = DB::table('Setting')->whereRaw("k = 'app_id' or k = 'app_key' or k = 'master_secret'")->pluck('v', 'k')->all();
            $notify = new SendAppNotify($setting['app_id'], $setting['app_key'], $setting['master_secret']);
            $notify->Push($member->ClientId, '买家提醒', '您有一笔待处理订单，赶快去处理把～');
        }
        return true;
    }
}
