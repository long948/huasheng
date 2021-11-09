<?php


namespace App\Http\Controllers;


use App\Services\MinerUserComputingPowerService;
use Illuminate\Http\Request;

/**
 * 用户算力
 * Class MinerUserComputingPowerController
 * @package App\Http\Controllers
 */
class MinerUserComputingPowerController extends Controller
{

    /**
     * @var Request
     */
    private $request;

    /**
     * @var MinerUserComputingPowerService
     */
    private $userComputingPowerService;


    /**
     * MinerUserComputingPowerController constructor.
     * @param Request $request
     * @param MinerUserComputingPowerService $service
     */
    public function __construct(Request $request,
                                MinerUserComputingPowerService $service)
    {
        $this->request = $request;
        $this->userComputingPowerService = $service;
    }


    /**
     * @OA\Get(
     *     path="/user-power-list",
     *     operationId="/user-power-list",
     *     tags={"花田"},
     *     summary="用户花田亩数列表",
     *     description="用户花田亩数列表",
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
    public function userComputingPowerList()
    {
        $user_id = $this->request->get('uid');
        self::success($this->userComputingPowerService->userComputingPowerList($user_id));
    }

}
