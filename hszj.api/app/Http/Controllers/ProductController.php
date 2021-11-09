<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function List(Request $request, ProductService $service){
        $count = intval($request->input('count'));
        $list = $service->List($count);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/product-detail",
     *     operationId="/product-detail",
     *     tags={"Product"},
     *     summary="矿机详情",
     *     description="矿机详情",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/ProductId"),
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
    public function Detail(Request $request, ProductService $service){
        $id = intval($request->input('Id'));
        $list = $service->Detail($id);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/product-newreg",
     *     operationId="/product-newreg",
     *     tags={"Product"},
     *     summary="新人福利",
     *     description="新人福利",
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
    public function NewReg(Request $request, ProductService $service){
        $uid = intval($request->get('uid'));
        $list = $service->NewReg($uid);
        return self::success($list);
    }

    /**
     * @OA\Post(
     *     path="/product-purchase",
     *     operationId="/product-purchase",
     *     tags={"Product"},
     *     summary="购买矿机",
     *     description="购买矿机",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/ProductId"),
     *     @OA\Parameter(ref="#/components/parameters/Number"),
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
    public function Purchase(Request $request, ProductService $service){
        $uid = intval($request->get('uid'));
        $pass = trim($request->input('PayPassword'));
        $service->VerifyPayPass($uid, $pass);
        $number = intval($request->input('Number'));
        $id = intval($request->input('Id'));
        $service->Purchase($uid, $id, $number);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/product-draw",
     *     operationId="/product-draw",
     *     tags={"Product"},
     *     summary="领取注册送的矿机",
     *     description="领取注册送的矿机",
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
    public function Draw(Request $request, ProductService $service){
        $uid = intval($request->get('uid'));
        $list = $service->Draw($uid);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/product-rush",
     *     operationId="/product-rush",
     *     tags={"Product"},
     *     summary="限时抢购列表",
     *     description="限时抢购列表",
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
    public function Rush(Request $request, ProductService $service){
        $list = $service->Rush();
        return self::success($list);
    }

    /**
     * @OA\Post(
     *     path="/product-rush-purchase",
     *     operationId="/product-rush-purchase",
     *     tags={"Product"},
     *     summary="预约抢购",
     *     description="预约抢购",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/RushId"),
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
    public function RushPurchase(Request $request, ProductService $service){
        $uid = intval($request->get('uid'));
        $id = intval($request->input('RushId'));
        $list = $service->RushPurchase($uid, $id);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/free-list",
     *     operationId="/free-list",
     *     tags={"Product"},
     *     summary="释放记录",
     *     description="释放记录",
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
    public function FreeList(Request $request, ProductService $service){
        $uid = intval($request->get('uid'));
        $count = intval($request->input('count'));
        $list = $service->FreeList($uid, $count);
        return self::success($list);
    }

    /**
     * @OA\Post(
     *     path="/balance-withdraw",
     *     operationId="/balance-withdraw",
     *     tags={"Product"},
     *     summary="提现",
     *     description="提现",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/WithdrawNumber"),
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
    public function Withdraw(Request $request, ProductService $service){
        $uid = intval($request->get('uid'));
        $number = $request->input('Number');
        $service->Withdraw($uid, $number);
        return self::success();
    }

    /**
     * @OA\Get(
     *     path="/balance-withdraw-detail",
     *     operationId="/balance-withdraw-detail",
     *     tags={"Product"},
     *     summary="产出提现",
     *     description="产出提现",
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
    public function WithdrawDetail(Request $request, ProductService $service){
        $uid = intval($request->get('uid'));
        $data = $service->WithdrawDetail($uid);
        return self::success($data);
    }

    /**
     * @OA\Get(
     *     path="/output-list",
     *     operationId="/output-list",
     *     tags={"Product"},
     *     summary="产出记录",
     *     description="产出记录",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/count"),
     *     @OA\Parameter(ref="#/components/parameters/OutputType"),
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
    public function Output(Request $request, ProductService $service){
        $uid = intval($request->get('uid'));
        $count = intval($request->input('count'));
        $type = intval($request->input('Type'));
        $list = $service->Output($uid, $type, $count);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/output-sum",
     *     operationId="/output-sum",
     *     tags={"Product"},
     *     summary="总产出",
     *     description="总产出",
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
    public function OutputSum(Request $request, ProductService $service){
        $uid = intval($request->get('uid'));
        $list = $service->OutputSum($uid);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/plan-recored",
     *     operationId="/plan-recored",
     *     tags={"Member"},
     *     summary="预约记录",
     *     description="预约记录",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/count"),
     *     @OA\Parameter(ref="#/components/parameters/PlanType"),
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
    public function PlanRecord(Request $request, ProductService $service){
        $uid = intval($request->get('uid'));
        $count = intval($request->input('count'));
        $type = intval($request->input('Type'));
        $list = $service->PlanRecord($uid, $type, $count);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/my-product",
     *     operationId="/my-product",
     *     tags={"Member"},
     *     summary="我的矿机",
     *     description="我的矿机",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/count"),
     *     @OA\Parameter(ref="#/components/parameters/MyProductType"),
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
    public function MyProduct(Request $request, ProductService $service){
        $uid = intval($request->get('uid'));
        $type = intval($request->input('Type'));
        $count = intval($request->input('count'));
        $list = $service->MyProduct($uid, $type, $count);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/my-product-detail",
     *     operationId="/my-product-detail",
     *     tags={"Member"},
     *     summary="矿机详情",
     *     description="矿机详情",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/MemberProductId"),
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
    public function MyProductDetail(Request $request, ProductService $service){
        $uid = intval($request->get('uid'));
        $id = intval($request->input('Id'));
        $list = $service->MyProductDetail($uid, $id);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/my-output-list",
     *     operationId="/output-list",
     *     tags={"Member"},
     *     summary="产出记录",
     *     description="产出记录",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/count"),
     *     @OA\Parameter(ref="#/components/parameters/MemberProductId"),
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
    public function MyOutput(Request $request, ProductService $service){
        $uid = intval($request->get('uid'));
        $id = intval($request->input('Id'));
        $count = intval($request->input('count'));
        $list = $service->MyOutput($uid, $id, $count);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/rush-log",
     *     operationId="/rush-log",
     *     tags={"Product"},
     *     summary="往期记录",
     *     description="往期记录",
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
    public function RushLog(Request $request, ProductService $service){
        $count = intval($request->input('count'));
        $list = $service->RushLog($count);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/rush-success",
     *     operationId="/rush-success",
     *     tags={"Product"},
     *     summary="中奖名单",
     *     description="中奖名单",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/count"),
     *     @OA\Parameter(ref="#/components/parameters/AddDate"),
     *     @OA\Parameter(ref="#/components/parameters/RushId"),
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
    public function RushSuccess(Request $request, ProductService $service){
        $count = intval($request->input('count'));
        $date = intval($request->input('AddDate'));
        $id = intval($request->input('RushId'));
        $list = $service->RushSuccess($date, $id, $count);
        return self::success($list);
    }

}
