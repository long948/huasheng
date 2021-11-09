<?php

namespace App\Http\Controllers;

use App\Exceptions\ArException;
use App\Http\Controllers\Controller;
use App\Models\CoinModel;
use App\Models\SettingModel;
use App\Services\CoinService;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class CoinController extends Controller
{

    /**
     * @OA\Get(
     *     path="/coin-list",
     *     operationId="/coin-list",
     *     tags={"Coin"},
     *     summary="币种列表",
     *     description="币种列表",
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
    public function List(Request $request, CoinService $service)
    {
        $userId = $request->get('uid');
        $list = $service->List($userId);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/single-coin",
     *     operationId="/single-coin",
     *     tags={"Coin"},
     *     summary="根据币种Id获取币种",
     *     description="根据币种Id获取币种",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/CoinId"),
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
    public function Single(Request $request, CoinService $service)
    {
        $id = intval($request->input('Id'));
        $list = $service->Single($id);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/coin-balance",
     *     operationId="/coin-balance",
     *     tags={"Coin"},
     *     summary="获取币种余额",
     *     description="获取币种余额",
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
    public function Balance(Request $request, CoinService $service)
    {
        $uid = intval($request->get('uid'));
        $list = $service->Balance($uid);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/coin-single-balance",
     *     operationId="/coin-single-balance",
     *     tags={"Coin"},
     *     summary="获取单个币种余额",
     *     description="获取单个币种余额",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/CoinId"),
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
    public function SingleBalance(Request $request, CoinService $service)
    {
        $uid = intval($request->get('uid'));
        $id = intval($request->input('Id')); //币种Id
        $list = $service->SingleBalance($uid, $id);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/withdraw-detail",
     *     operationId="/withdraw-detail",
     *     tags={"Coin"},
     *     summary="提币详情",
     *     description="提币详情",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/WithdrawId"),
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
    public function WithdrawDetail(Request $request, CoinService $service)
    {
        $uid = intval($request->get('uid'));
        $id = intval($request->input('Id')); //币种Id
        $list = $service->WithdrawDetail($uid, $id);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/recharge-detail",
     *     operationId="/recharge-detail",
     *     tags={"Coin"},
     *     summary="充值详情",
     *     description="充值详情",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/RechargeId"),
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
    public function RechargeDetail(Request $request, CoinService $service)
    {
        $uid = intval($request->get('uid'));
        $id = intval($request->input('Id'));
        $detail = $service->RechargeDetail($uid, $id);
        return self::success($detail);
    }

    /**
     * @OA\Get(
     *     path="/recharge-address",
     *     operationId="/recharge-address",
     *     tags={"Coin"},
     *     summary="获取充值地址",
     *     description="获取充值地址",
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
    public function RechargeAddress(Request $request, CoinService $service)
    {
        $uid = intval($request->get('uid'));
//        $id = DB::table('Coin')->where('EnName', 'PT')->value('Id');
        $id = intval($request->input('Id'));
        $address = $service->RechargeAddress($uid, $id);
        return self::success($address);
    }

    /**
     * @OA\Get(
     *     path="/recharge-withdraw",
     *     operationId="/recharge-withdraw",
     *     tags={"Coin"},
     *     summary="充提记录",
     *     description="充提记录",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/count"),
     *     @OA\Parameter(ref="#/components/parameters/CoinId"),
     *     @OA\Parameter(ref="#/components/parameters/Type"),
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
    public function RechargeAndWithdraw(Request $request, CoinService $service)
    {
        $uid = intval($request->get('uid'));
        $id = intval($request->input('Id'));
        $count = intval($request->input('count'));
        $type = intval($request->input('Type'));
        $list = $service->RechargeAndWithdraw($uid, $id, $count, $type);
        return self::success($list);
    }

    /**
     * @OA\Post(
     *     path="/withdraw",
     *     operationId="/withdraw",
     *     tags={"Coin"},
     *     summary="提现",
     *     description="提现",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/CoinId"),
     *     @OA\Parameter(ref="#/components/parameters/Money"),
     *     @OA\Parameter(ref="#/components/parameters/Address"),
     *     @OA\Parameter(ref="#/components/parameters/Memo"),
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
    public function Withdraw(Request $request, CoinService $service)
    {
        $uid = intval($request->get('uid'));
        $coinId = intval($request->input('Id'));
        $money = trim($request->input('Money'));
        $address = trim($request->input('Address'));
        $memo = trim($request->input('memo'));

        $AuthCode = trim($request->input('AuthCode'));

        $phone = DB::table('members')->where('id', $uid)->value('Phone');
        $auth = Redis::hget('userTransfer', $phone);
        if (empty($auth)) {
            throw new ArException(ArException::SELF_ERROR, '请先发送验证码');
        }
        $auth = json_decode($auth, true);
        if (bccomp($auth['verificationCount'], '0') == 0) {
            throw new ArException(ArException::SELF_ERROR, '验证异常,请重新发送');
        }
        $auth['verificationCount'] -= 1;
        Redis::hset('userTransfer', $phone, json_encode($auth));

        if (!is_array($auth)) {
            throw new ArException(ArException::SELF_ERROR, '验证码已失效，请重新发送');
        }
        if ($auth['Code'] != $AuthCode) {
            throw new ArException(ArException::SELF_ERROR, '验证码错误');
        }
        if ($auth['ExpireTime'] < time()) {
            throw new ArException(ArException::SELF_ERROR, '验证码已过期');
        }

        $pass = trim($request->input('PayPassword'));
        $service->VerifyPayPass($uid, $pass);
        $list = $service->Withdraw($uid, $coinId, $money, $address, $memo);
        return self::success($list);
    }

    /**
     * @OA\Post(
     *     path="/post.exchange",
     *     operationId="exchange",
     *     tags={"Coin"},
     *     summary="转账",
     *     description="转账",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/Money"),
     *     @OA\Parameter(ref="#/components/parameters/Address"),
     *     @OA\Parameter(ref="#/components/parameters/PayPassword"),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function exchange(Request $request, CoinService $service)
    {
        //临时加的
        $is_transfer = DB::table('setting')->where('k', 'is_transfer')->value('v') ?? 0;
        if ($is_transfer == 1) {
            throw new ArException(ArException::SELF_ERROR, '暂不支持转账,请稍后再试!');
        }
        $uid = intval($request->get('uid'));
        $coinId = DB::table('Coin')->where('EnName', 'PT')->value('Id');
        // $coinId = intval($request->input('Id'));
        $money = trim($request->input('Money'));
        $address = trim($request->input('Address'));
        $remark = trim($request->input('remark'));
        $AuthCode = trim($request->input('AuthCode'));

        $phone = DB::table('members')->where('id', $uid)->value('Phone');
        $auth = Redis::hget('userTransfer', $phone);
        if (empty($auth)) {
            throw new ArException(ArException::SELF_ERROR, '请先发送验证码');
        }
        $auth = json_decode($auth, true);
        if (bccomp($auth['verificationCount'], '0') == 0) {
            throw new ArException(ArException::SELF_ERROR, '验证异常,请重新发送');
        }
        $auth['verificationCount'] -= 1;
        Redis::hset('userTransfer', $phone, json_encode($auth));

        if (!is_array($auth)) {
            throw new ArException(ArException::SELF_ERROR, '验证码已失效，请重新发送');
        }
        if ($auth['Code'] != $AuthCode) {
            throw new ArException(ArException::SELF_ERROR, '验证码错误');
        }
        if ($auth['ExpireTime'] < time()) {
            throw new ArException(ArException::SELF_ERROR, '验证码已过期');
        }

        $pass = trim($request->input('PayPassword'));
        $service->VerifyPayPass($uid, $pass);
        $list = $service->exchange($uid, $coinId, $money, $address, $remark);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/get.exchange.fee",
     *     operationId="exchangeFee",
     *     tags={"Coin"},
     *     summary="转账手续费",
     *     description="转账手续费",
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
    public function exchangeFee(Request $request)
    {
        $uid = intval($request->get('uid'));
        return self::success(DB::table('Members')->where('Id', $uid)->value('Fee'));
        // return self::success(SettingModel::getValueByKey('exchange_fee'));
    }

    /**
     * @OA\Get(
     *     path="/get.wallet.info",
     *     operationId="walletInfo",
     *     tags={"Coin"},
     *     summary="钱包信息",
     *     description="钱包信息",
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
    public function walletInfo(Request $request)
    {
        $uid = intval($request->get('uid'));
        $tx_price = SettingModel::getValueByKey('tx_price');
        $coins = CoinModel::getCoinAll();
        $userService = new MemberService();
        $userService->addMemebrCoin($uid);
        $result = [];
        foreach ($coins as $coin) {
            $user_coin = DB::table('MemberCoin')->where([
                'MemberId' => $uid,
                'CoinId' => $coin->Id,
            ])->first();
            $result[] = [
                'Id' => $coin->Id,
                'EnName' => $coin->EnName,
                'FullName' => $coin->FullName,
                'Logo' => $coin->Logo,
                'CoinPrice' => $coin->Price,
                'MoneyUSDT' => bcmul($coin->Price, ($user_coin->Money + $user_coin->Forzen), 4),
                'Available' => $user_coin->Money,
                'Forzen' => $user_coin->Forzen,
                'PriceCny' => $coin->PriceCny,
                'IsWithDraw' => $coin->IsWithDraw,
                'IsRecharge' => $coin->IsRecharge,
            ];
        }
        
        return self::success($result);
    }

}
