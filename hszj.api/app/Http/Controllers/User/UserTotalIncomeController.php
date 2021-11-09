<?php


namespace App\Http\Controllers\User;


use App\Exceptions\ArException;
use App\Http\Controllers\Controller;
use App\Services\Bill\BillUserTotalIncomeService;
use App\Services\TradeService;
use App\Services\User\UserTotalIncomeService;
use Illuminate\Http\Request;

class UserTotalIncomeController extends Controller
{

    /**
     * @var Request
     */
    private $request;

    /**
     * @var BillUserTotalIncomeService
     */
    private $billUserTotalIncomeService;

    /**
     * @var UserTotalIncomeService
     */
    private $userTotalIncomeService;

    /**
     * @var TradeService
     */
    private $tradeService;

    /**
     * UserTotalIncomeController constructor.
     * @param Request $request
     * @param BillUserTotalIncomeService $billUserTotalIncomeService
     * @param UserTotalIncomeService $userTotalIncomeService
     * @param TradeService $tradeService
     */
    public function __construct(Request $request,
                                BillUserTotalIncomeService $billUserTotalIncomeService,
                                UserTotalIncomeService $userTotalIncomeService,
                                TradeService $tradeService)
    {
        $this->request = $request;
        $this->billUserTotalIncomeService = $billUserTotalIncomeService;
        $this->userTotalIncomeService = $userTotalIncomeService;
        $this->tradeService = $tradeService;
    }


    /**
     * @OA\Get(
     *     path="/user-income-list",
     *     operationId="/user-income-list",
     *     tags={"仓库"},
     *     summary="仓库账单记录",
     *     description="仓库账单记录",
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/count"),
     *     @OA\Parameter(ref="#/components/parameters/beginTime"),
     *     @OA\Parameter(ref="#/components/parameters/endTime"),
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
    public function list()
    {
        $userId = $this->request->get('uid');
        $page = $this->request->input('page');
        $count = $this->request->input('count');
        $beginTime = $this->request->input('beginTime');
        $endTime = $this->request->input('endTime');
        self::success($this->billUserTotalIncomeService->list($userId, $page, $count, $beginTime, $endTime));
    }


    /**
     * @OA\Post(
     *     path="/user-income-list",
     *     operationId="/user-income-list",
     *     tags={"仓库"},
     *     summary="转出至账户",
     *     description="转出至账户",
     *     @OA\Parameter(ref="#/components/parameters/amount"),
     *     @OA\Parameter(ref="#/components/parameters/pay_password"),
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
    public function transfer()
    {
        $userId = $this->request->get('uid');
        $amount = $this->request->input('amount');
        $pay_password = $this->request->input('pay_password');
        if (!intval($amount) > 0) {
            throw new ArException(ArException::SELF_ERROR, '金额不正确');
        }
        if (empty($pay_password)) {
            throw new ArException(ArException::SELF_ERROR, '支付密码不能为空');
        }
        $this->tradeService->VerifyPayPass($userId, $pay_password);
        self::success($this->userTotalIncomeService->transfer($userId, $amount));
    }

    /**
     * 账户转入至备用斤
     * @throws ArException
     */
    public function turning()
    {
        $userId = $this->request->get('uid');
        $amount = $this->request->input('amount');
        $pay_password = $this->request->input('pay_password');
        if ($amount <= 0 || !is_numeric($amount) || $amount < 0.0001) {
            throw new ArException(ArException::SELF_ERROR, '金额不正确');
        }
        if (empty($pay_password)) {
            throw new ArException(ArException::SELF_ERROR, '支付密码不能为空');
        }
        $this->tradeService->VerifyPayPass($userId, $pay_password);

        self::success($this->userTotalIncomeService->turning($userId, $amount));
    }
}
