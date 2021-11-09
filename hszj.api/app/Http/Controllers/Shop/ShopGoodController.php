<?php


namespace App\Http\Controllers\Shop;


use App\Exceptions\ArException;
use App\Http\Controllers\Controller;
use App\Models\SettingModel;
use App\Services\Shop\ShopGoodService;
use Illuminate\Http\Request;

class ShopGoodController extends Controller
{

    /**
     * @var ShopGoodService
     */
    private $goodService;

    /**
     * @var Request
     */
    private $request;


    /**
     * ShopGoodController constructor.
     * @param ShopGoodService $goodService
     * @param Request $request
     */
    public function __construct(ShopGoodService $goodService, Request $request)
    {
        $this->goodService = $goodService;
        $this->request = $request;
    }


    /**
     * 拼购功能属于移植功能，部分固定参数不能更改
     * @throws ArException
     */
    public function details()
    {
        $goodId = $this->request->input('goodId');
        $spikeId = $this->request->input('spikeId');
        $userId = $this->request->get('uid');
        $teamType = 2; //团类型 2秒杀 不能更改
        self::success($this->goodService->foundGoodDetails($goodId, $userId, $teamType, $spikeId));
    }


    /**
     * 商品规则
     */
    public function shopRule()
    {
        self::success(SettingModel::getValueByKey('shop_rule'));
    }

}
