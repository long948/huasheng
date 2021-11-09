<?php


namespace App\Http\Controllers\Dog;


use App\Exceptions\ArException;
use App\Http\Controllers\Controller;
use App\Services\Dog\DogListService;
use App\Services\Dog\DogUserService;
use Illuminate\Http\Request;

class DogController extends Controller
{

    /**
     * @var DogListService
     */
    protected $dogListService;

    /**
     * @var Request
     */
    protected $request;

    /**
     * DogController constructor.
     * @param DogListService $dogListService
     * @param Request $request
     */
    public function __construct(DogListService $dogListService,
                                Request $request)
    {
        $this->dogListService = $dogListService;
        $this->request = $request;
    }

    /**
     * @OA\Get(
     *     path="/buy-dog-list",
     *     operationId="/buy-dog-list",
     *     tags={"大黄"},
     *     summary="购买大黄列表",
     *     description="购买大黄列表",
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
    public function buyList()
    {
        $userId = $this->request->get('uid');
        $dogId = $this->request->input('dog_id');
        self::success($this->dogListService->list());
    }

}
