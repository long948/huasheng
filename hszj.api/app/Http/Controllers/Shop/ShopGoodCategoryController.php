<?php


namespace App\Http\Controllers\Shop;


use App\Http\Controllers\Controller;
use App\Services\Shop\ShopGoodCategoryService;

class ShopGoodCategoryController extends Controller
{

    /**
     * @var ShopGoodCategoryService
     */
    private $goodCategoryService;

    /**
     * ShopGoodCategoryController constructor.
     * @param ShopGoodCategoryService $goodCategoryService
     */
    public function __construct(ShopGoodCategoryService $goodCategoryService)
    {
        $this->goodCategoryService = $goodCategoryService;
    }

    
    public function category()
    {
        self::success($this->goodCategoryService->category());
    }

}
