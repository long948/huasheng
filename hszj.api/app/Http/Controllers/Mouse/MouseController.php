<?php


namespace App\Http\Controllers\Mouse;


use App\Exceptions\ArException;
use App\Http\Controllers\Controller;
use App\Services\Mouse\MouseUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;

class MouseController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var MouseUserService
     */
    private $mouseUserService;

    /**
     * MouseController constructor.
     * @param Request $request
     * @param MouseUserService $mouseUserService
     */
    public function __construct(Request $request,
                                MouseUserService $mouseUserService)
    {
        $this->request = $request;
        $this->mouseUserService = $mouseUserService;
    }

    /**
     * @OA\Post(
     *     path="/user-buy-mouse",
     *     operationId="/user-buy-mouse",
     *     tags={"花鼠"},
     *     summary="购买花鼠",
     *     description="购买花鼠",
     *     @OA\Parameter(ref="#/components/parameters/mouse_id"),
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
    public function buyMouse()
    {
        $userId = $this->request->get('uid');
        $mouseId = $this->request->input('mouse_id');
        self::success($this->mouseUserService->buy($userId, $mouseId));
    }

    /**
     * @OA\Get(
     *     path="/user-mouse-list",
     *     operationId="/user-mouse-list",
     *     tags={"花鼠"},
     *     summary="我的老鼠列表",
     *     description="我的老鼠列表",
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
        self::success($this->mouseUserService->list($userId));
    }

    /**
     * @OA\Get(
     *     path="/user-mouse-steal-users",
     *     operationId="/user-mouse-steal-users",
     *     tags={"花鼠"},
     *     summary="偷取用户列表",
     *     description="偷取用户列表",
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
    public function getStealUsers()
    {
        $userId = $this->request->get('uid');
        self::success($this->mouseUserService->getStealUsers($userId));
    }

    /**
     * @OA\Post(
     *     path="/user-mouse-steal",
     *     operationId="/user-mouse-steal",
     *     tags={"花鼠"},
     *     summary="花鼠偷取收益",
     *     description="花鼠偷取收益",
     *     @OA\Parameter(ref="#/components/parameters/mouse_id"),
     *     @OA\Parameter(ref="#/components/parameters/steal_user_id"),
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
    public function stealIncome()
    {
        $userId = $this->request->get('uid');
        $mouseId = $this->request->input('mouse_id');
        $stealUserId = $this->request->input('steal_user_id');
        if (Str::is($userId, $stealUserId)) {
            throw new ArException(ArException::SELF_ERROR, '抱歉,不能偷取自己');
        }
        $is_steal = false;
        $userStealUsers = $this->mouseUserService->getStealUsers($userId);
        foreach ($userStealUsers as $userStealUser) {
            if (Str::is($stealUserId, $userStealUser->id)) {
                $is_steal = true;
            }
        }

        if (!$is_steal) {
            throw new ArException(ArException::SELF_ERROR, '抱歉,您不能偷取他的');
        }

        $amount = $this->mouseUserService->stealIncome($userId, $mouseId, $stealUserId);
        self::success($amount);
    }

}
