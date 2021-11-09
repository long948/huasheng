<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TradeService;

class TradeController extends Controller
{

    /**
     * @OA\Post(
     *     path="/trade-purchase",
     *     operationId="/trade-purchase",
     *     tags={"Trade"},
     *     summary="购买",
     *     description="购买",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *  @OA\Parameter(ref="#/components/parameters/CoinNumber"),
     *  @OA\Parameter(ref="#/components/parameters/PayMethod"),
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
    public function Purchase(Request $request, TradeService $service){
        $uid = intval($request->get('uid'));
        $number = trim($request->input('Number'));
        $method = intval($request->input('PayMethod'));
        $id = $service->Purchase($uid, $number, $method);
        return self::success($id);
    }

    /**
     * @OA\Post(
     *     path="/trade-sell",
     *     operationId="/trade-sell",
     *     tags={"Trade"},
     *     summary="卖",
     *     description="卖",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/CoinNumber"),
     *     @OA\Parameter(ref="#/components/parameters/PayPassword"),
     *     @OA\Parameter(ref="#/components/parameters/PayMethod"),
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
    public function Sell(Request $request, TradeService $service){
        $uid = intval($request->get('uid'));
        $pass = trim($request->input('PayPassword'));
        $service->VerifyPayPass($uid, $pass);
        $number = trim($request->input('Number'));
        $payMethod = intval($request->input('PayMethod'));
        $oid = $service->Sell($uid, $number, $payMethod);
        return self::success($oid);
    }

    /**
     * @OA\Get(
     *     path="/trade-list",
     *     operationId="/trade-list",
     *     tags={"Trade"},
     *     summary="订单记录",
     *     description="订单记录",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *  @OA\Parameter(ref="#/components/parameters/page"),
     *  @OA\Parameter(ref="#/components/parameters/count"),
     *  @OA\Parameter(ref="#/components/parameters/TradeType"),
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
    public function List(Request $request, TradeService $service){
        $uid = intval($request->get('uid'));
        $count = intval($request->input('count'));
        $type = intval($request->input('Type'));
        $list = $service->List($uid, $type, $count);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/trade-detail",
     *     operationId="/trade-detail",
     *     tags={"Trade"},
     *     summary="订单详情",
     *     description="订单详情",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *  @OA\Parameter(ref="#/components/parameters/OrderNumber"),
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
    public function Detail(Request $request, TradeService $service){
        $uid = intval($request->get('uid'));
        $oid = trim($request->input('OrderNumber'));
        $list = $service->Detail($uid, $oid);
        return self::success($list);
    }

    /**
     * @OA\Post(
     *     path="/trade-pay",
     *     operationId="/trade-pay",
     *     tags={"Trade"},
     *     summary="确认付款",
     *     description="确认付款",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OrderNumber"),
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
    public function Pay(Request $request, TradeService $service){
        $uid = intval($request->get('uid'));
        $oid = trim($request->input('OrderNumber'));
        $service->Pay($uid, $oid);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/trade-collections",
     *     operationId="/trade-collections",
     *     tags={"Trade"},
     *     summary="确认收款并放币",
     *     description="确认收款并放币",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OrderNumber"),
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
    public function Collections(Request $request, TradeService $service){
        $uid = intval($request->get('uid'));
        $oid = trim($request->input('OrderNumber'));
        $service->Collections($uid, $oid);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/trade-cancle",
     *     operationId="/trade-cancle",
     *     tags={"Trade"},
     *     summary="取消订单",
     *     description="取消订单",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OrderNumber"),
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
    public function Cancle(Request $request, TradeService $service){
        $uid = intval($request->get('uid'));
        $oid = trim($request->input('OrderNumber'));
        $service->Cancle($uid, $oid);
        return self::success();
    }

}
