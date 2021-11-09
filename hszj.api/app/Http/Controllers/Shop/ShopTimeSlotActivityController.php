<?php


namespace App\Http\Controllers\Shop;


use App\Http\Controllers\Controller;
use App\Services\Shop\ShopTimeSlotActivityService;

class ShopTimeSlotActivityController extends Controller
{

    /**
     * @var ShopTimeSlotActivityService
     */
    private $timeSlotActivityService;

    /**
     * ShopTimeSlotActivityController constructor.
     * @param ShopTimeSlotActivityService $timeSlotActivityService
     */
    public function __construct(ShopTimeSlotActivityService $timeSlotActivityService)
    {
        $this->timeSlotActivityService = $timeSlotActivityService;
    }


    public function timeSlotActivityGood()
    {
        self::success($this->timeSlotActivityService->timeSlotActivityGood());
    }

}
