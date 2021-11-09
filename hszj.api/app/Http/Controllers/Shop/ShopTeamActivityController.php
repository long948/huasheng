<?php


namespace App\Http\Controllers\Shop;


use App\Http\Controllers\Controller;
use App\Services\Shop\ShopTeamActivityService;

class ShopTeamActivityController extends Controller
{


    /**
     * @var ShopTeamActivityService
     */
    private $teamActivityService;


    /**
     * ShopTeamActivityController constructor.
     * @param ShopTeamActivityService $teamActivityService
     */
    public function __construct(ShopTeamActivityService $teamActivityService)
    {
        $this->teamActivityService = $teamActivityService;
    }

}
