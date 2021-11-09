<?php

namespace App\Listeners;

use App\Console\Commands\UserFactor;
use App\Console\Commands\UserFactorLog;
use App\Events\FactorEvent;
use App\Events\SaplingEvent;
use App\Models\UserFactorLogModel;
use App\Models\UserFactorModel;
use App\Services\MinerUserSaplingService;
use Illuminate\Events\Dispatcher;

/**
 * 树苗事件观察者(得到树苗后立即释放能量球)
 * Class FactorEventSubscriber
 * @package App\Listeners
 */
class SaplingEventSubscriber
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 产生能量和能量球
     * @param SaplingEvent $event
     * @throws \Throwable
     */
    public function sapling(SaplingEvent $event)
    {
        $userSaplingService = new MinerUserSaplingService();
        $userSaplingService->dayUserSaplingRelease($event->user_id);
    }

    /**
     * 为订阅者注册监听器
     *
     * @param Dispatcher $events
     */
    public function subscribe($events)
    {
        /**
         * 监听多个事件分发给不同的方法处理
         */
//        $events->listen(
//            SaplingEvent::class,
//            'App\Listeners\SaplingEventSubscriber@sapling'
//        );
    }
}
