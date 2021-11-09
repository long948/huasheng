<?php


namespace App\Http\Controllers\Shop;


use App\Exceptions\ArException;
use App\Http\Controllers\Controller;
use App\Services\Shop\ShopTeamFollowService;
use App\Services\TradeService;
use Illuminate\Http\Request;

class ShopPayController extends Controller
{

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ShopTeamFollowService
     */
    private $teamFollowService;

    /**
     * @var TradeService
     */
    private $tradeService;

    /**
     * ShopPayController constructor.
     * @param Request $request
     * @param ShopTeamFollowService $teamFollowService
     * @param TradeService $tradeService
     */
    public function __construct(Request $request,
                                ShopTeamFollowService $teamFollowService,
                                TradeService $tradeService)
    {
        $this->request = $request;
        $this->teamFollowService = $teamFollowService;
        $this->tradeService = $tradeService;
    }


    /**
     * @throws ArException
     */
    public function pay()
    {
        $orderSn = $this->request->input('orderSn');
        $password = $this->request->input('password');
        $userId = $this->request->get('uid');

        //验证支付密码
        $this->tradeService->VerifyPayPass($userId, $password);

        //是否是拼团订单
        $follow = $this->teamFollowService->isFollow($orderSn);
        if (empty($follow)) {
            throw new ArException(ArException::SHOP_USER_FOLLOW_NOT_EXISTS);
        }
        
        $result = $this->teamFollowService->userFoundPay($userId, $orderSn);
        if (!$result) {
            throw new ArException(ArException::SHOP_FOUND_END);
        }

        $this->success();
    }

}
