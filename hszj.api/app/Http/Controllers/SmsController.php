<?php

namespace App\Http\Controllers;

use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{

    /**
     * @OA\Post(
     *     path="/sms-modify-paypass",
     *     operationId="/sms-modify-paypass",
     *     tags={"SMS"},
     *     summary="忘记交易密码验证码",
     *     description="忘记交易密码验证码",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Phone")
     * )
     */
    public function ModifyPayPassCode(Request $request, SmsService $service){
        $phone =  trim($request->input('Phone'));

        //注册
        $service->ModifyPayPassCode($phone);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/sms-modify-pass",
     *     operationId="/sms-modify-pass",
     *     tags={"SMS"},
     *     summary="忘记密码验证码",
     *     description="忘记密码验证码",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Phone")
     * )
     */
    public function ModifyPassCode(Request $request, SmsService $service){
        $phone =  trim($request->input('Phone'));

        //注册
        $service->ModifyPassCode($phone);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/sms-register-code",
     *     operationId="/sms-register-code",
     *     tags={"SMS"},
     *     summary="发送注册验证码",
     *     description="发送注册验证码",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Phone")
     * )
     */
    public function RegisterCode(Request $request, SmsService $service){
        $phone =  trim($request->input('Phone'));

        //注册
        $service->RegisterCode($phone);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/sms-bindpay-code",
     *     operationId="/sms-bindpay-code",
     *     tags={"SMS"},
     *     summary="绑定支付验证码",
     *     description="绑定支付验证码",
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
    public function BindPayCode(Request $request, SmsService $service){
        $uid = intval($request->get('uid'));
        //注册
        $service->BindPayCode($uid);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/sms-setpaypass-code",
     *     operationId="/sms-setpaypass-code",
     *     tags={"SMS"},
     *     summary="设置交易密码验证码",
     *     description="设置交易密码验证码",
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
    public function SetPayPassCode(Request $request, SmsService $service){
        $uid = intval($request->get('uid'));
        //注册
        $service->SetPayPassCode($uid);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/sms-unbind-code",
     *     operationId="unbindCode",
     *     tags={"SMS"},
     *     summary="发送解绑手机验证码",
     *     description="发送解绑手机验证码",
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
    public function unbindCode(Request $request, SmsService $service){
        $uid = intval($request->get('uid'));

        $service->unbindCode($uid);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/sms-vcode",
     *     operationId="vCode",
     *     tags={"SMS"},
     *     summary="发送验证码",
     *     description="发送验证码",
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
    public function vCode(Request $request, SmsService $service)
    {
        $uid = intval($request->get('uid'));

        $service->vCode($uid);
        return self::success();
    }


    /**
     * @param Request $request
     * @param SmsService $service
     * @throws \App\Exceptions\ArException
     */
    public function transfer(Request $request, SmsService $service)
    {
        $uid = intval($request->get('uid'));
        $service->transfer($uid);
        return self::success();
    }
}
