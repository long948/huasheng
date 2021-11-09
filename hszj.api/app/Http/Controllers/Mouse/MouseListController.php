<?php


namespace App\Http\Controllers\Mouse;


use App\Exceptions\ArException;
use App\Http\Controllers\Controller;
use App\Services\Mouse\MouseListService;
use App\Services\Mouse\MouseUserService;
use Illuminate\Http\Request;
use Psr\SimpleCache\InvalidArgumentException;

class MouseListController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var MouseListService
     */
    private $mouseListService;

    /**
     * MouseListController constructor.
     * @param Request $request
     * @param MouseListService $mouseListService
     */
    public function __construct(Request $request,
                                MouseListService $mouseListService)
    {
        $this->request = $request;
        $this->mouseListService = $mouseListService;
    }

    /**
     * @OA\Get(
     *     path="/buy-mouse-list",
     *     operationId="/buy-mouse-list",
     *     tags={"花鼠"},
     *     summary="获取可购买花鼠",
     *     description="获取可购买花鼠",
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
    public function mouseList()
    {
        $userId = $this->request->get('uid');
        self::success($this->mouseListService->list());
    }
}
