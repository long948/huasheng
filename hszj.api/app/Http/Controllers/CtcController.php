<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SettingModel;
use App\Services\CtcService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CtcController extends Controller
{

    /**
     * @OA\Get(
     *     path="/ctc-coin",
     *     operationId="/ctc-coin",
     *     tags={"CTC"},
     *     summary="交易币种",
     *     description="交易币种",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/CTCType"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function Coin(Request $request, CtcService $service)
    {
        $type = intval($request->input('Type'));
        $list = $service->Coin($type);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/ctc-list",
     *     operationId="/ctc-list",
     *     tags={"CTC"},
     *     summary="交易列表",
     *     description="交易列表",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/count"),
     *     @OA\Parameter(ref="#/components/parameters/filter"),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function List(Request $request, CtcService $service)
    {
        $count = intval($request->input('count'));
        // $type = intval($request->input('Type'));
        $type = 2;//求购单
        $id = DB::table('Coin')->where('EnName', 'PT')->value('Id');
        // $id = SettingModel::getValueByKey('ctc_coin');
        // $id = intval($request->input('Id'));
        // $isBank = intval($request->input('IsBank'));
        // $isWechat = intval($request->input('IsWechat'));
        // $isAlipay = intval($request->input('IsAlipay'));
        // $minMoney = $request->input('MinMoney');
        $filter = (string)$request->input('filter', '');
        $setting = DB::table('CTCSetting')->first();
        if ($setting->start_time <= date('H') && $setting->end_time >= date('H')) {
            $list = $service->List($type, $count, $id, $filter);
            return self::success([
                'start_time' => $setting->start_time,
                'end_time' => $setting->end_time,
                'data' => $list
            ]);
            //return self::success($list);
        } else {
            return self::success([
                'start_time' => $setting->start_time,
                'end_time' => $setting->end_time,
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/ctc-my-list",
     *     operationId="/ctc-my-list",
     *     tags={"CTC"},
     *     summary="我的发布",
     *     description="我的发布",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/count"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function MyList(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $count = intval($request->input('count'));
        $type = 2;
        // $type = intval($request->input('Type'));
        $list = $service->MyList($uid, $type, $count);
        return self::success($list);
    }

    /**
     * @OA\Post(
     *     path="/add-sell-order",
     *     operationId="/add-sell-order",
     *     tags={"CTC"},
     *     summary="发布单",
     *     description="发布单",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Number"),
     *     @OA\Parameter(ref="#/components/parameters/IsAddress"),
     *     @OA\Parameter(ref="#/components/parameters/IsWechat"),
     *     @OA\Parameter(ref="#/components/parameters/IsAlipay"),
     *     @OA\Parameter(ref="#/components/parameters/IsBank"),
     *     @OA\Parameter(ref="#/components/parameters/PayPassword"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function AddSellOrder(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $coinId = DB::table('Coin')->where('EnName', 'PT')->value('Id');
        // $coinId = SettingModel::getValueByKey('ctc_coin');
        // $coinId = intval($request->input('Id'));
        $number = trim($request->input('Number'));
        $type = 2;
        // $type = intval($request->input('Type'));
        // $price = trim($request->input('Price'));
        $price = SettingModel::getValueByKey('tx_price');
        // $minMoney = trim($request->input('MinMoney'));
        // $maxMoney = trim($request->input('MaxMoney'));
        $isBank = intval($request->input('IsBank'));
        $isAddress = intval($request->input('IsAddress'));
        $isWechat = intval($request->input('IsWechat'));
        $isAlipay = intval($request->input('IsAlipay'));
        $pass = trim($request->input('PayPassword'));
        // $service->VerifyPayPass($uid, $pass);
        $service->AddSellOrder($uid, $coinId, $number, $price, $isAddress, $isWechat, $isAlipay, $isBank, $type);
        return self::success();
    }


    /**
     * @OA\Post(
     *     path="/ctc-order-stop",
     *     operationId="/ctc-order-stop",
     *     tags={"CTC"},
     *     summary="终止",
     *     description="终止",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OrderId"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function OrderStop(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $id = intval($request->input('Id'));
        $service->OrderStop($uid, $id);
        return self::success();
    }


    /**
     * @OA\Post(
     *     path="/ctc-buy",
     *     operationId="/ctc-buy",
     *     tags={"CTC"},
     *     summary="购买",
     *     description="购买",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OrderId"),
     *     @OA\Parameter(ref="#/components/parameters/Number"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function Buy(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $id = intval($request->input('Id'));
        $number = trim($request->input('Number'));
        $oid = $service->Buy($uid, $id, $number);
        return self::success($oid);
    }

    /**
     * @OA\Post(
     *     path="/ctc-sell",
     *     operationId="/ctc-sell",
     *     tags={"CTC"},
     *     summary="出售",
     *     description="出售",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OrderId"),
     *     @OA\Parameter(ref="#/components/parameters/IsAddress"),
     *     @OA\Parameter(ref="#/components/parameters/IsWechat"),
     *     @OA\Parameter(ref="#/components/parameters/IsAlipay"),
     *     @OA\Parameter(ref="#/components/parameters/IsBank"),
     *     @OA\Parameter(ref="#/components/parameters/AuthCode"),
     *     @OA\Parameter(ref="#/components/parameters/PayPassword"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function Sell(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $id = intval($request->input('Id'));
        // $number = trim($request->input('Number'));
        $IsAddress = intval($request->input('IsAddress'));
        $isbank = intval($request->input('IsBank'));
        $isWechat = intval($request->input('IsWechat'));
        $isAlipay = intval($request->input('IsAlipay'));
        $AuthCode = trim($request->input('AuthCode'));
        $pass = trim($request->input('PayPassword'));
        // $service->VerifyPayPass($uid, $pass);
        $oid = $service->Sell($uid, $id, $IsAddress, $isWechat, $isAlipay, $isbank, $AuthCode);
        return self::success($oid);
    }

    /**
     * @OA\Get(
     *     path="/ctc-trade-list",
     *     operationId="/ctc-trade-list",
     *     tags={"CTC"},
     *     summary="订单列表",
     *     description="订单列表",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/count"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function TradeList(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $count = intval($request->input('count'));
        $type = 2;
        // $type = intval($request->input('Type'));
        $list = $service->TradeList($uid, $type, $count);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/ctc-trade-my-list",
     *     operationId="/ctc-trade-my-list",
     *     tags={"CTC"},
     *     summary="订单列表(求购与出售)",
     *     description="订单列表(求购与出售)",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/order_type"),
     *     @OA\Parameter(ref="#/components/parameters/order_status"),
     *     @OA\Parameter(ref="#/components/parameters/min"),
     *     @OA\Parameter(ref="#/components/parameters/max"),
     *     @OA\Parameter(ref="#/components/parameters/start"),
     *     @OA\Parameter(ref="#/components/parameters/end"),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/count"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function TradeMyList(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $order_type = intval($request->input('order_type'));
        $order_status = intval($request->input('order_status'));
        $min = $request->input('min');
        $max = $request->input('max');
        $start = $request->input('start');
        $end = $request->input('end');
        $count = intval($request->input('count'));
        $list = $service->TradeMyList($uid, $order_type, $order_status, $min, $max, $start, $end, $count);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/ctc-trade-detail",
     *     operationId="/ctc-trade-detail",
     *     tags={"CTC"},
     *     summary="订单详情",
     *     description="订单详情",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OrderId"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function TradeDetail(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $id = intval($request->input('Id'));
        $list = $service->TradeDetail($uid, $id);
        return self::success($list);
    }

    /**
     * @OA\Post(
     *     path="/ctc-trade-pay",
     *     operationId="/ctc-trade-pay",
     *     tags={"CTC"},
     *     summary="付款",
     *     description="付款",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OrderId"),
     *     @OA\Parameter(ref="#/components/parameters/Imgs"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function TradePay(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $id = intval($request->input('Id'));
        // $method = intval($request->input('PayMethod'));
        $imgs = trim($request->input('Imgs'));
        $service->TradePay($uid, $id, $imgs);
        return self::success();
    }

    /**
     * @OA\Get(
     *     path="/ctc-pay-method",
     *     operationId="/ctc-pay-method",
     *     tags={"CTC"},
     *     summary="各种配置",
     *     description="各种配置",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function PayMethod(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $data = DB::table('CTCSetting')->first();
        $member = DB::table('Members')->where('Id', $uid)->first();
        if ($member->IsOpenFee == 1) {
            $data->RecvFee = $member->RecvFee;
            $data->SellFee = $member->Fee;
        }
        return self::success($data);
    }

    /**
     * @OA\Post(
     *     path="/ctc-confirm",
     *     operationId="/ctc-confirm",
     *     tags={"CTC"},
     *     summary="确认收款",
     *     description="确认收款",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OrderId"),
     *     @OA\Parameter(ref="#/components/parameters/PayPassword"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function Confirm(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $id = intval($request->input('Id'));
        $pass = trim($request->input('PayPassword'));
        $service->VerifyPayPass($uid, $pass);
        $service->Confirm($uid, $id);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/ctc-cancle",
     *     operationId="/ctc-cancle",
     *     tags={"CTC"},
     *     summary="取消订单",
     *     description="取消订单",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OrderId"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function Cancle(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $id = intval($request->input('Id'));
        $service->Cancle($uid, $id);
        return self::success();
    }

    /**
     * @OA\Get(
     *     path="/ctc-info",
     *     operationId="/ctc-info",
     *     tags={"CTC"},
     *     summary="订单信息",
     *     description="订单信息",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OrderId"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function Info(Request $request, CtcService $service)
    {
        $id = intval($request->input('Id'));
        $info = $service->Info($id);
        return self::success($info);
    }

    /**
     * @OA\Get(
     *     path="/ctc-member-info",
     *     operationId="/ctc-member-info",
     *     tags={"CTC"},
     *     summary="个人资料",
     *     description="个人资料",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OrderId"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function MemberInfo(Request $request, CtcService $service)
    {
        $id = intval($request->input('Id'));

        $info = [];

        //白名单用户可查看信息
        $white_list = SettingModel::getValueByKey('white_list');
        if ($white_list) {
            if (in_array($id, explode(',', $white_list))) {
                $info = $service->MemberInfo($id);
            }
        }
        return self::success($info);
    }

    /**
     * @OA\Get(
     *     path="/ctc-appeal-reason",
     *     operationId="/ctc-appeal-reason",
     *     tags={"CTC"},
     *     summary="申诉原因",
     *     description="申诉原因",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function AppealReason(Request $request, CtcService $service)
    {
        $info = $service->AppealReason();
        return self::success($info);
    }

    /**
     * @OA\Post(
     *     path="/ctc-appeal",
     *     operationId="/ctc-appeal",
     *     tags={"CTC"},
     *     summary="申诉",
     *     description="申诉",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/ReasonId"),
     *     @OA\Parameter(ref="#/components/parameters/Content"),
     *     @OA\Parameter(ref="#/components/parameters/OrderId"),
     *     @OA\Parameter(ref="#/components/parameters/Imgs"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function Appeal(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $reasonId = intval($request->input('ReasonId'));
        $content = trim($request->input('Content'));
        $id = trim($request->input('Id'));
        $imgs = trim($request->input('Imgs'));
        $service->Appeal($uid, $id, $reasonId, $content, $imgs);
        return self::success();
    }

    /**
     * @OA\Get(
     *     path="/get.ctc.info",
     *     operationId="getCtcInfo",
     *     tags={"CTC"},
     *     summary="",
     *     description="ctc信息",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     * )
     */
    public function getCtcInfo()
    {
        $filter = SettingModel::getValueByKey('ctc_filter');
        $tx_price = SettingModel::getValueByKey('tx_price');
        $tx_fee = SettingModel::getValueByKey('tx_fee');
        $buy_amount = DB::table('CTCOrder')
            ->where('State', 0)
            //->where('AddTime', '>=', strtotime(date('Y-m-d 0:0:0', time())))
            //->where('AddTime', '<=', strtotime(date('Y-m-d 23:59:59', time())))
            ->where('Type', 2)
            ->sum('Number');
        $data = DB::table('Setting')->where('k', 'buy_amount')->get();
        foreach ($data as &$v) {
            $add_amount = $v->v;

            $by_amounts = $buy_amount + $add_amount;
        }
        $tx_amount = DB::table('CTCTrade')
            ->where('State', 2)
            ->where('AddTime', '>=', strtotime(date('Y-m-d 0:0:0', time())))
            ->where('AddTime', '<=', strtotime(date('Y-m-d 23:59:59', time())))
            ->sum('Number');
        return self::success([
            'filter' => ($filter ? explode(',', $filter) : []),
            'coin_price' => sprintf('%.4f', $tx_price),
            'coin_price_usdt' => sprintf('%.4f', bcmul($tx_price, 7, 3)),
            'buy_amount' => sprintf('%.3f', $by_amounts),
            'tx_amount' => sprintf('%.3f', $tx_amount),
            'fee' => sprintf('%.2f', $tx_fee),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/post.ctc.unfrozen",
     *     operationId="unfrozen",
     *     tags={"CTC"},
     *     summary="解冻",
     *     description="解冻",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function unfrozen(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $service->unfrozen($uid);
        return self::success();
    }

    /**
     * @OA\Get(
     *     path="/post.ctc.remain",
     *     operationId="remain",
     *     tags={"CTC"},
     *     summary="提醒",
     *     description="提醒",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function remain(Request $request, CtcService $service)
    {
        $uid = intval($request->get('uid'));
        $id = trim($request->input('Id'));
        $service->remain($uid, $id);
        return self::success();
    }
}
