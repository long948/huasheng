<?php


namespace App\Http\Controllers;


use App\Events\SaplingEvent;
use App\Exceptions\ArException;
use App\Jobs\UserTeamQueue;
use App\Services\MinerUserComputingPowerService;
use App\Services\MinerUserSaplingPackageService;
use App\Services\MinerUserSaplingService;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * 用户树苗管理
 * Class MinerSaplingController
 * @package App\Http\Controllers
 */
class MinerUserSaplingController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var MinerUserSaplingService
     */
    private $userSaplingService;

    /**
     * @var MinerUserComputingPowerService
     */
    private $userComputingPowerService;


    /**
     * MinerUserSaplingController constructor.
     * @param UserService $userService
     * @param Request $request
     * @param MinerUserSaplingService $userSaplingService
     * @param MinerUserComputingPowerService $userComputingPowerService
     */
    public function __construct(UserService $userService,
                                Request $request,
                                MinerUserSaplingService $userSaplingService,
                                MinerUserComputingPowerService $userComputingPowerService)
    {
        $this->userService = $userService;
        $this->request = $request;
        $this->userSaplingService = $userSaplingService;
        $this->userComputingPowerService = $userComputingPowerService;
    }


    /**
     * @OA\get(
     *     path="/sapling-user-home",
     *     operationId="/sapling-user-home",
     *     tags={"首页"},
     *     summary="首页",
     *     description="首页",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/userHome")
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function userHome()
    {
        $user_id = $this->request->input('user_id');
        if (empty($user_id)) {
            $user_id = $this->request->get('uid');
        }

        $type = (int)$this->request->input('type', 1);
        if ($type != 1 && $type != 2) {
            throw new ArException(ArException::SELF_ERROR, '参数错误');
        }
        $result = $this->userService->index($user_id, $type);
        //event(new SaplingEvent($user_id));
        self::success($result);
    }


    /**
     * @OA\get(
     *     path="/sapling-user-list",
     *     operationId="/sapling-user-list",
     *     tags={"花田"},
     *     summary="用户花田列表",
     *     description="用户花田列表",
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
    public function userSaplingList()
    {
        $user_id = $this->request->get('uid');
        $userSaplingList = $this->userSaplingService->userSaplingList($user_id);
        self::success($userSaplingList);
    }


    /**
     * @OA\get(
     *     path="/sapling-user-details",
     *     operationId="/sapling-user-details",
     *     tags={"花田"},
     *     summary="用户花田详情",
     *     description="用户花田详情",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sapling_id"),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function userSaplingDetail()
    {
        $user_id = $this->request->get('uid');
        $sapling_id = $this->request->input('sapling_id');
        $result = (array)$this->userSaplingService->userSaplingDetail($user_id, $sapling_id);
        if ($result) {
            $result['details'] = $this->userSaplingService->getUserSaplingIncome($user_id, $result['user_sapling_id']);
        }
        self::success($result);
    }

    /**
     * @OA\Get(
     *     path="/user-my-team-sapling-info",
     *     operationId="/user-my-team-sapling-info",
     *     tags={"团队信息"},
     *     summary="团队信息",
     *     description="团队信息",
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
    public function userMyTeamSaplingInfo()
    {
        $user_id = $this->request->get('uid');
        $list = $this->userComputingPowerService->mySelfTeamInfo($user_id);
        self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/user-auth-giveAway",
     *     operationId="/user-auth-giveAway",
     *     tags={"花田"},
     *     summary="实名认证后赠送花田",
     *     description="实名认证后赠送花田",
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
    public function UserIsGiveAwayByAuth()
    {
        $user_id = $this->request->get('uid');
        $result = $this->userSaplingService->giveAwayByAuth($user_id);
        if ($result) {
            self::success();
        }
        throw new ArException(ArException::SELF_ERROR, '领取错误,请稍后再试!');
    }

    /**
     * @OA\Get(
     *     path="/user-auth-is-giveAway",
     *     operationId="/user-auth-is-giveAway",
     *     tags={"花田"},
     *     summary="获取用户是否领取花田",
     *     description="获取用户是否领取花田",
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
    public function userIsGiveAway()
    {
        $user_id = $this->request->get('uid');
        $sapling = $this->userSaplingService->userAuthIsGiveAway($user_id);
        if ($sapling->isNotEmpty()) {
            self::success(['is_giveAway' => 1]);
        }
        self::success(['is_giveAway' => 0]);
    }
}
