<?php


namespace App\Http\Controllers;


use App\Exceptions\ArException;
use App\Services\EcologyOrderService;
use App\Services\TradeService;
use Illuminate\Http\Request;

class EcologyOrderController extends Controller
{

    public function order(Request $request, EcologyOrderService $service, TradeService $tradeService)
    {
        $user_id = $request->get('uid');
        $type = $request->input('type', 1);
        $child_type = $request->input('child_type', 0);
        $nickname = $request->input('nickname', 0);
        $phone = $request->input('phone', 0);
        $address = $request->input('address', 0);
        $card_num = $request->input('card_num', 0);
        $card_id = $request->input('card_id', 0);
        $amount = $request->get('amount');
        $pay_password = $request->get('pay_password');
        if (empty($pay_password)) {
            throw new ArException(ArException::SELF_ERROR, '交易密码不能为空');
        }
        $tradeService->VerifyPayPass($user_id, $pay_password);

        self::success($service->order($user_id, $type, $child_type, $nickname, $phone, $address, $card_num, $card_id, $amount));
    }


    public function orderList(Request $request, EcologyOrderService $service)
    {
        $user_id = $request->get('uid');
        $page = intval($request->input('page', 1));
        $count = intval($request->input('count', 20));
        self::success($service->orderList($user_id, $page, $count));
    }


    public function info(EcologyOrderService $service)
    {
        self::success($service->info());
    }

}
