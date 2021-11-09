<?php

namespace App\Http\Controllers;

use App\Exceptions\ArException;
use App\Services\IndexService;
use App\Services\MemberService;
use App\Services\UserGiveAwayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{

    /**
     * @OA\Post(
     *     path="/member-forget-paypassword",
     *     operationId="/member-forget-paypassword",
     *     tags={"Member"},
     *     summary="忘记交易密码",
     *     description="忘记交易密码",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Phone"),
     *     @OA\Parameter(ref="#/components/parameters/AuthCode"),
     *     @OA\Parameter(ref="#/components/parameters/NewPayPassword"),
     *     @OA\Parameter(ref="#/components/parameters/RepeatPayPassword")
     * )
     */
    public function ForgetPayPassword(Request $request, MemberService $service)
    {
        $code = intval($request->input('AuthCode'));
        $phone = trim($request->input('Phone'));
        $pass = trim($request->input('NewPayPassword'));
        $repass = trim($request->input('RepeatPayPassword'));
        $service->ForgetPayPassword($code, $phone, $pass, $repass);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/member-forget-password",
     *     operationId="/member-forget-password",
     *     tags={"Member"},
     *     summary="忘记密码",
     *     description="忘记密码",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Phone"),
     *     @OA\Parameter(ref="#/components/parameters/AuthCode"),
     *     @OA\Parameter(ref="#/components/parameters/NewPassword"),
     *     @OA\Parameter(ref="#/components/parameters/RepeatPassword")
     * )
     */
    public function ForgetPassword(Request $request, MemberService $service)
    {
        $code = intval($request->input('AuthCode'));
        $phone = trim($request->input('Phone'));
        $pass = trim($request->input('NewPassword'));
        $repass = trim($request->input('RepeatPassword'));
        $service->ForgetPassword($code, $phone, $pass, $repass);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/member-modify-avatar",
     *     operationId="/member-modify-avatar",
     *     tags={"Member"},
     *     summary="修改头像",
     *     description="修改头像",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Avatar"),
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
    public function ModifyAvatar(Request $request, MemberService $service)
    {
        $name = trim($request->input('Avatar'));
        $uid = intval($request->get('uid'));
        $service->ModifyAvatar($uid, $name);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/member-modify-nick",
     *     operationId="/member-modify-nick",
     *     tags={"Member"},
     *     summary="修改昵称",
     *     description="修改昵称",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/NickName"),
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
    public function ModifyNickName(Request $request, MemberService $service)
    {
        $name = trim($request->input('NickName'));
        $uid = intval($request->get('uid'));
        $service->ModifyNickName($uid, $name);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/member-modify-paypassword",
     *     operationId="/member-modify-paypassword",
     *     tags={"Member"},
     *     summary="修改交易密码",
     *     description="修改交易密码",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OldPayPassword"),
     *     @OA\Parameter(ref="#/components/parameters/PayPassword"),
     *     @OA\Parameter(ref="#/components/parameters/RepeatPayPassword"),
     *     @OA\Parameter(ref="#/components/parameters/AuthCode"),
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
    public function ModifyPayPassword(Request $request, MemberService $service)
    {
        $password = trim($request->input('PayPassword'));
        $repeatPasswrod = trim($request->input('RepeatPayPassword'));
        $oldPassword = trim($request->input('OldPayPassword'));
        $uid = intval($request->get('uid'));
        $AuthCode = trim($request->input('AuthCode'));
        $service->ModifyPayPassword($uid, $oldPassword, $password, $repeatPasswrod, $AuthCode);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/member-set-paypassword",
     *     operationId="/member-set-paypassword",
     *     tags={"Member"},
     *     summary="设置交易密码",
     *     description="设置交易密码",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/PayPassword"),
     *     @OA\Parameter(ref="#/components/parameters/RepeatPayPassword"),
     *     @OA\Parameter(ref="#/components/parameters/AuthCode"),
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
    public function SetPayPassword(Request $request, MemberService $service)
    {
        $password = trim($request->input('PayPassword'));
        $repeatPasswrod = trim($request->input('RepeatPayPassword'));
        $uid = intval($request->get('uid'));
        $code = trim($request->input('AuthCode'));
        $service->SetPayPassword($uid, $password, $repeatPasswrod, $code);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/member-modify-password",
     *     operationId="/member-modify-password",
     *     tags={"Member"},
     *     summary="修改登录密码",
     *     description="修改登录密码",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OldPassword"),
     *     @OA\Parameter(ref="#/components/parameters/Password"),
     *     @OA\Parameter(ref="#/components/parameters/RepeatPassword"),
     *     @OA\Parameter(ref="#/components/parameters/AuthCode"),
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
    public function ModifyPassword(Request $request, MemberService $service)
    {
        $password = trim($request->input('Password'));
        $repeatPasswrod = trim($request->input('RepeatPassword'));
        $oldPassword = trim($request->input('OldPassword'));
        $uid = intval($request->get('uid'));
        $AuthCode = trim($request->input('AuthCode'));
        $service->ModifyPassword($uid, $oldPassword, $password, $repeatPasswrod, $AuthCode);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/post.modifyphone",
     *     operationId="ModifyPhone",
     *     tags={"Member"},
     *     summary="修改手机号",
     *     description="修改手机号",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OldAuthCode"),
     *     @OA\Parameter(ref="#/components/parameters/NewAuthCode"),
     *     @OA\Parameter(ref="#/components/parameters/NewPhone"),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function ModifyPhone(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        $OldAuthCode = trim($request->input('OldAuthCode'));
        $NewAuthCode = trim($request->input('NewAuthCode'));
        $NewPhone = trim($request->input('NewPhone'));
        $service->ModifyPhone($uid, $OldAuthCode, $NewAuthCode, $NewPhone);
        return self::success();
    }

    /**
     * @OA\Get(
     *     path="/team",
     *     operationId="/team",
     *     tags={"Member"},
     *     summary="团队",
     *     description="团队",
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
    public function Group(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        $in = $service->Group($uid);
        return self::success($in);
    }

    /**
     * @OA\Get(
     *     path="/invite-list",
     *     operationId="/invite-list",
     *     tags={"Member"},
     *     summary="有效直推列表",
     *     description="有效直推列表",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/count"),
     *     @OA\Parameter(ref="#/components/parameters/auth_status"),
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
    public function InviteList(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        $count = intval($request->input('count'));
        $auth_status = intval($request->input('auth_status', 1));
        $list = $service->InviteList($uid, $count, $auth_status);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/member-info",
     *     operationId="/member-info",
     *     tags={"Member"},
     *     summary="用户资料",
     *     description="用户资料",
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
    public function Info(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        $in = $service->Info($uid);
        return self::success($in);
    }

    /**
     * @OA\Post(
     *     path="/member-login",
     *     operationId="/member-login",
     *     tags={"Member"},
     *     summary="账号密码登录",
     *     description="账号密码登录",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Phone"),
     *     @OA\Parameter(ref="#/components/parameters/Password"),
     *     @OA\Parameter(ref="#/components/parameters/ClientId"),
     *     @OA\Parameter(ref="#/components/parameters/captcha"),
     *     @OA\Parameter(ref="#/components/parameters/rand"),
     *
     * )
     */
    public function Login(Request $request, MemberService $service, IndexService $indexService)
    {
        $phone = trim($request->input('Phone'));
        $password = trim($request->input('Password'));
        $ClientId = trim($request->input('ClientId'));
        $ClientId = 1;

        $rand = trim($request->input('rand'));
        if (empty($rand)) throw new ArException(ArException::SELF_ERROR, '请输入随机码');

        if (empty($ClientId)) {
            throw new ArException(ArException::SELF_ERROR, '请输入客户端编号');
        }
        
        if (empty($password)) {
            throw new ArException(ArException::SELF_ERROR, '请输入密码');
        }

        $captcha = trim($request->input('captcha'));
        if (empty($captcha)) {
            throw new ArException(ArException::SELF_ERROR, '请输入图形验证码');
        }
        $indexService->checkCaptcha($captcha, $rand);

        //登录
        $token = $service->Login($phone, $password, $ClientId);
        return self::success($token);
    }

    /**
     * @OA\Post(
     *     path="/member-register",
     *     operationId="/member-register",
     *     tags={"Member"},
     *     summary="注册",
     *     description="注册",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Phone"),
     *     @OA\Parameter(ref="#/components/parameters/Password"),
     *     @OA\Parameter(ref="#/components/parameters/RepeatPassword"),
     *     @OA\Parameter(ref="#/components/parameters/InviteCode"),
     *     @OA\Parameter(ref="#/components/parameters/AuthCode"),
     *     @OA\Parameter(ref="#/components/parameters/ClientId")
     * )
     */
    public function Register(Request $request, MemberService $service)
    {
        $ClientId = trim($request->input('ClientId'));
        $data = [
            'Phone' => trim($request->input('Phone')),
            'Password' => trim($request->input('Password')),
            'RepeatPassword' => trim($request->input('RepeatPassword')),
            // 'PayPassword' => trim($request->input('PayPassword')),
            // 'RepeatPayPassword' => trim($request->input('RepeatPayPassword')),
            'InviteCode' => intval($request->input('InviteCode')),
            'AuthCode' => intval($request->input('AuthCode')),
            // 'NickName' => trim($request->input('NickName')),
            'Ip' => $request->getClientIp()
        ];
        //注册
        $service->Register($data);

        $token = $service->Login($data['Phone'], $data['Password'], $ClientId);
        return self::success($token);
    }

    /**
     * @OA\Get(
     *     path="/finace-list",
     *     operationId="/finace-list",
     *     tags={"Stream"},
     *     summary="资金变动列表",
     *     description="资金变动列表",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/min"),
     *     @OA\Parameter(ref="#/components/parameters/max"),
     *     @OA\Parameter(ref="#/components/parameters/year"),
     *     @OA\Parameter(ref="#/components/parameters/month"),
     *     @OA\Parameter(ref="#/components/parameters/TxType"),
     *     @OA\Parameter(ref="#/components/parameters/TurnoverType"),
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
    public function FinaceList(Request $request, MemberService $service)
    {
        $tx_type = intval($request->input('TxType'));
        $min = $request->input('min');
        $max = $request->input('max');
        $type = intval($request->input('Type'));
        $uid = intval($request->get('uid'));
        $count = intval($request->input('count'));
        $month = intval($request->input('month'));
        $year = intval($request->input('year'));
        $coinId = intval($request->input('coinId', 0));
        $list = $service->List($uid, $type, $tx_type, $min, $max, $year, $month, $coinId, $count);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/finace-molds",
     *     operationId="/finace-molds",
     *     tags={"Stream"},
     *     summary="资金变动类型",
     *     description="资金变动类型",
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
    public function FinaceMolds(Request $request, MemberService $service)
    {
        $list = $service->Molds();
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/self",
     *     operationId="/self",
     *     tags={"Member"},
     *     summary="我的",
     *     description="我的",
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
    public function My(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        $list = $service->My($uid);
        return self::success($list);
    }

    /**
     * @OA\Post(
     *     path="/bind-bank",
     *     operationId="/bind-bank",
     *     tags={"Member"},
     *     summary="绑定银行卡",
     *     description="绑定银行卡",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/RealName"),
     *     @OA\Parameter(ref="#/components/parameters/Phone"),
     *     @OA\Parameter(ref="#/components/parameters/Bank"),
     *     @OA\Parameter(ref="#/components/parameters/Card"),
     *     @OA\Parameter(ref="#/components/parameters/AuthCode"),
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
    public function BindBank(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        $name = trim($request->input('Name'));
        $phone = trim($request->input('Phone'));
        $bank = trim($request->input('Bank'));
        $card = trim($request->input('Card'));
        $code = trim($request->input('AuthCode'));
        $service->BindBank($uid, $name, $phone, $bank, $card, $code);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/modify-bank",
     *     operationId="/modify-bank",
     *     tags={"Member"},
     *     summary="修改银行卡",
     *     description="修改银行卡",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/RealName"),
     *     @OA\Parameter(ref="#/components/parameters/Phone"),
     *     @OA\Parameter(ref="#/components/parameters/Bank"),
     *     @OA\Parameter(ref="#/components/parameters/Card"),
     *     @OA\Parameter(ref="#/components/parameters/AuthCode"),
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
    public function ModifyBank(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        $name = trim($request->input('Name'));
        $phone = trim($request->input('Phone'));
        $bank = trim($request->input('Bank'));
        $card = trim($request->input('Card'));
        $code = trim($request->input('AuthCode'));
        $service->ModifyBank($uid, $name, $phone, $bank, $card, $code);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/bind-wechat",
     *     operationId="/bind-wechat",
     *     tags={"Member"},
     *     summary="绑定微信",
     *     description="绑定微信",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Account"),
     *     @OA\Parameter(ref="#/components/parameters/QrCode"),
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
    public function BindWeChat(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        // $name = trim($request->input('NickName'));
        $account = trim($request->input('Account'));
        $qrcode = trim($request->input('QrCode'));
        // $code = trim($request->input('AuthCode'));
        $service->BindWeChat($uid, $account, $qrcode);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/modify-wechat",
     *     operationId="/modify-wechat",
     *     tags={"Member"},
     *     summary="修改微信",
     *     description="修改微信",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Account"),
     *     @OA\Parameter(ref="#/components/parameters/QrCode"),
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
    public function ModifyWeChat(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        // $name = trim($request->input('NickName'));
        $account = trim($request->input('Account'));
        $qrcode = trim($request->input('QrCode'));
        $service->ModifyWeChat($uid, $account, $qrcode);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/bind-alipay",
     *     operationId="/bind-alipay",
     *     tags={"Member"},
     *     summary="绑定支付宝",
     *     description="绑定支付宝",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Account"),
     *     @OA\Parameter(ref="#/components/parameters/QrCode"),
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
    public function BindAlipay(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        // $name = trim($request->input('NickName'));
        $account = trim($request->input('Account'));
        $qrcode = trim($request->input('QrCode'));
        // $code = trim($request->input('AuthCode'));
        $service->BindAlipay($uid, $account, $qrcode);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/modify-alipay",
     *     operationId="/modify-alipay",
     *     tags={"Member"},
     *     summary="修改支付宝",
     *     description="修改支付宝",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Account"),
     *     @OA\Parameter(ref="#/components/parameters/QrCode"),
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
    public function ModifyAlipay(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        // $name = trim($request->input('NickName'));
        $account = trim($request->input('Account'));
        $qrcode = trim($request->input('QrCode'));
        // $code = trim($request->input('AuthCode'));
        $service->ModifyAlipay($uid, $account, $qrcode);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/bind-adress",
     *     operationId="BindAddress",
     *     tags={"Member"},
     *     summary="绑定usdt地址",
     *     description="绑定usdt地址",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Account"),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function BindAddress(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        $account = trim($request->input('Account'));
        $service->BindAddress($uid, $account);
        return self::success();
    }

    /**
     * @OA\Post(
     *     path="/modify-adress",
     *     operationId="/modify-address",
     *     tags={"Member"},
     *     summary="修改usdt地址",
     *     description="修改usdt地址",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Account"),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function ModifyAddress(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        $account = trim($request->input('Account'));
        // $code = trim($request->input('AuthCode'));
        $service->ModifyAddress($uid, $account);
        return self::success();
    }

    /**
     * @OA\Get(
     *     path="/bind-pay-info",
     *     operationId="/bind-pay-info",
     *     tags={"Member"},
     *     summary="支付信息",
     *     description="支付信息",
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
    public function PayInfo(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        $info = $service->PayInfo($uid);
        return self::success($info);
    }

    /**
     * @OA\Post(
     *     path="/auth-member",
     *     operationId="/auth-member",
     *     tags={"Member"},
     *     summary="实名认证",
     *     description="实名认证",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/IdCard"),
     *     @OA\Parameter(ref="#/components/parameters/AuthName"),
     *     @OA\Parameter(ref="#/components/parameters/MemberIdCardVideo"),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function Auth(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        $idCard = trim($request->input('IdCard'));
        $name = trim($request->input('Name'));
        // $imgs = $request->input('IdCardImg');
        $video = $request->input('video');
        // $shou_fron_image = $request->input('shou_fron_image');
        $front_image = $request->input('front_image');
        // $reverse_image = $request->input('reverse_image');
        $type = 2;
        // $type = $request->input('type');

        $list = $service->Auth($uid, $video, $idCard, $name, $type, $front_image);
        // $list = $service->Auth($uid, $video, $front_image, $reverse_image, $type);
        return self::success($list);
    }

    /**
     * @OA\Post(
     *     path="/ocr",
     *     operationId="/ocr",
     *     tags={"Member"},
     *     summary="OCR识别",
     *     description="OCR识别",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/MemberIdCardFrontImage"),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function ocr(Request $request)
    {
        $front_image = $request->input('front_image');

        return self::success([
            'real_name' => '测试姓名',
            'id_card' => '50012312312314324',
        ]);


//        $appCode = env("AUTH_APP_CODE");
//        $qiniu = DB::table('QiniuConfig')->value('Domain');
//        $faceFile = $qiniu . $front_image;
//        $authData = AutoRecognitionService::push($appCode, $faceFile, "face");
//
//        if (empty($authData["name"]) || empty($authData["num"])) throw new ArException(ArException::SELF_ERROR, '识别失败');
//        return self::success([
//            'real_name' => isset($authData["name"]) ? $authData["name"] : '',
//            'id_card' => isset($authData["num"]) ? $authData["num"] : '',
//        ]);
    }

    /**
     * @OA\Get(
     *     path="/auth-info",
     *     operationId="/auth-info",
     *     tags={"Member"},
     *     summary="实名认证",
     *     description="实名认证",
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
    public function AuthInfo(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        $list = $service->AuthInfo($uid);
        return self::success($list);
    }

    /**
     * @OA\Post(
     *     path="/auth-safe",
     *     operationId="/auth-safe",
     *     tags={"Member"},
     *     summary="安全",
     *     description="安全",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/OpenPayPass"),
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
    public function Safe(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        $open = intval($request->input('OpenPayPass'));
        $service->Safe($uid, $open);
        return self::success();
    }

    /**
     * @OA\Get(
     *     path="/wechat-group",
     *     operationId="/wechat-group",
     *     tags={"Member"},
     *     summary="微信群",
     *     description="微信群",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     )
     * )
     */
    public function WechatGroup(Request $request, MemberService $service)
    {
        $list = DB::table('WechatGroup')->where('IsOpen', 1)->get();
        foreach ($list as $item) {
            $item->QrCode = $service->QiniuDomain() . '/' . $item->QrCode;
        }
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/get.tx.power",
     *     operationId="txPower",
     *     tags={"Member"},
     *     summary="交易特权",
     *     description="交易特权",
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
    public function txPower(Request $request, MemberService $service)
    {
        $uid = intval($request->get('uid'));
        $data = $service->txPower($uid);
        return self::success($data);
    }

    /**
     * @OA\Get(
     *     path="/get.share",
     *     operationId="share",
     *     tags={"Member"},
     *     summary="分享页背景图",
     *     description="分享页背景图",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     )
     * )
     */
    public function share()
    {
        $data = DB::table('Setting')->whereRaw("k = 'background' or k = 'posters_background' or k = 'posters_copywriting'")->pluck('v', 'k');

        foreach ($data as $k => $v) {
            if ($k == 'posters_background') $data[$k] = json_decode($v);
        }
        return $this->success($data);
    }


    /**
     * @OA\Get(
     *     path="/member-giveaway-list",
     *     operationId="member-giveaway-list",
     *     tags={"Member"},
     *     summary="赠送账户记录",
     *     description="赠送账户记录",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     )
     * )
     */
    public function userGiveAwayBill(Request $request, UserGiveAwayService $service)
    {
        $uid = intval($request->get('uid'));
        $page = intval($request->input('page', 1));
        $count = intval($request->input('count', 20));
        $data = $service->list($uid, $page, $count);
        return self::success($data);
    }

}
