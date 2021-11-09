<?php


namespace App\Listeners;

use App\Events\UserRegisterEvent;
use App\Services\MinerUserLevelService;
use App\Services\User\UserTotalIncomeService;
use App\Services\UserGiveAwayService;
use Illuminate\Support\Facades\Log;

class UserRegisteredSubscriber
{

    /**
     * @var UserTotalIncomeService
     */
    protected $userTotalIncomeService;

    /**
     * @var MinerUserLevelService
     */
    protected $userLevelService;

    /**
     * @var UserGiveAwayService
     */
    protected $userGiveAwayService;

    /**
     * UserRegisteredSubscriber constructor.
     * @param UserTotalIncomeService $userTotalIncomeService
     * @param MinerUserLevelService $userLevelService
     * @param UserGiveAwayService $userGiveAwayService
     */
    public function __construct(UserTotalIncomeService $userTotalIncomeService,
                                MinerUserLevelService $userLevelService,
                                UserGiveAwayService $userGiveAwayService)
    {
        $this->userTotalIncomeService = $userTotalIncomeService;
        $this->userLevelService = $userLevelService;
        $this->userGiveAwayService = $userGiveAwayService;
    }


    public function register(UserRegisterEvent $event)
    {
        //初始化用户仓库
        $this->userTotalIncomeService->initUserTotalIncome($event->getUserId());
        //初始化用户等级
        $this->userLevelService->initUserLevel($event->getUserId(), 1);
        //初始化用户赠送金额账户
        $this->userGiveAwayService->initUserTotalIncome($event->getUserId());
    }


    public function subscribe($events)
    {
        $events->listen(
            UserRegisterEvent::class,
            'App\Listeners\UserRegisteredSubscriber@register'
        );
    }
}
