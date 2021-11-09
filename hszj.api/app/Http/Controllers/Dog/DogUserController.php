<?php


namespace App\Http\Controllers\Dog;


use App\Http\Controllers\Controller;
use App\Services\Dog\DogUserService;
use App\Utils\Enum\Enums;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DogUserController extends Controller
{
    /**
     * @var DogUserService
     */
    protected $dogUserService;

    /**
     * @var Request
     */
    protected $request;

    /**
     * DogUserController constructor.
     * @param DogUserService $dogUserService
     * @param Request $request
     */
    public function __construct(DogUserService $dogUserService,
                                Request $request)
    {
        $this->dogUserService = $dogUserService;
        $this->request = $request;
    }


    /**
     * @OA\Post(
     *     path="/buy-dog-list",
     *     operationId="/buy-dog-list",
     *     tags={"大黄"},
     *     summary="购买大黄",
     *     description="购买大黄",
     *     @OA\Parameter(ref="#/components/parameters/dog_id"),
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
    public function buyDog()
    {
        $userId = $this->request->get('uid');
        $dogId = $this->request->input('dog_id');
        self::success($this->dogUserService->buy($userId, $dogId));
    }


    /**
     * @OA\Post(
     *     path="/user-dog-stand_guard",
     *     operationId="/user-dog-stand_guard",
     *     tags={"大黄"},
     *     summary="设置大黄站岗",
     *     description="设置大黄站岗",
     *     @OA\Parameter(ref="#/components/parameters/user_dog_id"),
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
    public function standGuard()
    {
        $userId = $this->request->get('uid');
        $dogId = $this->request->input('user_dog_id');
        self::success($this->dogUserService->standGuardUp($userId, $dogId));
    }


    /**
     * @OA\Get(
     *     path="/user-dog-list",
     *     operationId="/user-dog-list",
     *     tags={"大黄"},
     *     summary="我的大黄列表",
     *     description="我的大黄列表",
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
        self::success($this->dogUserService->list($userId));
    }

    /**
     * @OA\Get(
     *     path="/user-dog-is-stand-guard",
     *     operationId="/user-dog-is-stand-guard",
     *     tags={"大黄"},
     *     summary="正在站岗的大黄",
     *     description="正在站岗的大黄",
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
    public function isStandGuard()
    {
        $userId = $this->request->get('uid');
        $key = Enums::CACHE_KEY['USER_DOG_STAND_GUARD_UP'] . $userId;
        self::success(Cache::get($key));
    }

}
