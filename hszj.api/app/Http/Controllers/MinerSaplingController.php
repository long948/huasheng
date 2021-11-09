<?php


namespace App\Http\Controllers;


use App\Services\MinerSaplingService;
use Illuminate\Http\Request;

/**
 * 树苗列表
 * Class MinerSaplingController
 * @package App\Http\Controllers
 */
class MinerSaplingController extends Controller
{

    /**
     * @var Request
     */
    private $request;

    /**
     * @var MinerSaplingService
     */
    private $minerSaplingService;

    /**
     * MinerSaplingController constructor.
     * @param Request $request
     * @param MinerSaplingService $minerSaplingService
     */
    public function __construct(Request $request,
                                MinerSaplingService $minerSaplingService)
    {
        $this->request = $request;
        $this->minerSaplingService = $minerSaplingService;
    }

    /**
     * @OA\get(
     *     path="/sapling-list",
     *     operationId="/sapling-list",
     *     tags={"花田"},
     *     summary="获取可购买花田列表",
     *     description="获取可购买花田列表",
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
    public function saplingList()
    {
        $user_id = $this->request->get('uid');
        self::success($this->minerSaplingService->saplingList($user_id, 1));
    }

    /**
     * @OA\get(
     *     path="/sapling-details",
     *     operationId="/sapling-details",
     *     tags={"花田"},
     *     summary="获取花田详情",
     *     description="获取花田详情",
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
    public function saplingDetail()
    {
        $sapling_id = $this->request->get('sapling_id');
        self::success($this->minerSaplingService->saplingDetail($sapling_id));
    }

}
