<?php


namespace App\Http\Controllers\Store;


use App\Exceptions\ArException;
use App\Http\Controllers\Controller;
use App\Services\Dog\DogListService;
use App\Services\Dog\DogUserService;
use App\Services\MemberService;
use App\Services\MinerSaplingService;
use App\Services\MinerUserSaplingService;
use App\Services\Mouse\MouseListService;
use App\Services\Mouse\MouseUserService;
use App\Services\TradeService;
use App\Utils\Enum\Enums;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class StoreController extends Controller
{


    /**
     * @var MinerSaplingService
     */
    private $minerSaplingService;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var MouseListService
     */
    private $mouseListService;

    /**
     * @var DogListService
     */
    private $dogListService;

    /**
     * @var DogUserService
     */
    private $dogUserService;

    /**
     * @var MouseUserService
     */
    private $mouseUserService;

    /**
     * @var MinerUserSaplingService
     */
    private $userSaplingService;

    /**
     * @var TradeService
     */
    private $tradeService;

    /**
     * 商店缓存时间
     * @var int
     */
    private $cacheTime = 3600;

    /**
     * StoreController constructor.
     * @param MinerSaplingService $minerSaplingService
     * @param Request $request
     * @param MouseListService $mouseListService
     * @param DogListService $dogListService
     * @param DogUserService $dogUserService
     * @param MouseUserService $mouseUserService
     * @param MinerUserSaplingService $userSaplingService
     * @param TradeService $tradeService
     */
    public function __construct(
        MinerSaplingService $minerSaplingService,
        Request $request,
        MouseListService $mouseListService,
        DogListService $dogListService,
        DogUserService $dogUserService,
        MouseUserService $mouseUserService,
        MinerUserSaplingService $userSaplingService,
        TradeService $tradeService)
    {
        $this->minerSaplingService = $minerSaplingService;
        $this->request = $request;
        $this->mouseListService = $mouseListService;
        $this->dogListService = $dogListService;
        $this->dogUserService = $dogUserService;
        $this->mouseUserService = $mouseUserService;
        $this->userSaplingService = $userSaplingService;
        $this->tradeService = $tradeService;
    }


    /**
     * @OA\Get(
     *     path="/store",
     *     operationId="/store",
     *     tags={"商店"},
     *     summary="商店列表",
     *     description="商店列表",
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
    public function store()
    {
        $userId = $this->request->get('uid');
        $result['sapling'] = $this->minerSaplingService->saplingList($userId, true);
        $result['mouse'] = $this->mouseListService->list($userId);
        $result['dog'] = $this->dogListService->list($userId);
        self::success($result);
    }


    /**
     * 购买
     */
    public function buy()
    {
        $userId = $this->request->get('uid');
        $type = $this->request->input('type');
        $id = $this->request->get('id');
        $payPassword = $this->request->input('PayPassword');

        if (empty($payPassword)) {
            throw new ArException(ArException::SELF_ERROR, '交易密码不能为空');
        }

        $this->tradeService->VerifyPayPass($userId, $payPassword);

        if (Str::is($type, 'sapling')) {
            $result = $this->userSaplingService->buySapling($userId, $id);
            if ($result) {
                //兑换矿机开启出售限制
                (new MemberService())->updateFrozenCTCSellStatus($userId, true);
                // event(new SaplingEvent($userId));
            }
        }

        if (Str::is($type, 'mouse')) {
            $this->mouseUserService->buy($userId, $id);
        }

        if (Str::is($type, 'dog')) {

            if (Str::is($id, '211171920811460612')) {
                $number = $this->request->input('number', 1);
                $result = $this->dogUserService->equityDividend($userId, $id, $number);
                //直接添加到股权
            } else {
                $result = $this->dogUserService->buy($userId, $id);
            }
        }

        self::success();
    }

    /**
     * 详情
     */
    public function details()
    {
        $type = $this->request->input('type');
        $id = $this->request->get('id');
        $result = [];
        if (Str::is($type, 'sapling')) {
            $result = $this->minerSaplingService->saplingDetail($id);
        }
        if (Str::is($type, 'mouse')) {
            $result = $this->mouseListService->find($id);
        }
        if (Str::is($type, 'dog')) {
            $result = $this->dogListService->find($id);
        }

        self::success($result);
    }
}
