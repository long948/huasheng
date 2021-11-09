<?php

namespace App\Listeners;

use App\Console\Commands\UserFactor;
use App\Console\Commands\UserFactorLog;
use App\Events\FactorEvent;
use App\Events\UserDogFailureEvent;
use App\Models\UserFactorLogModel;
use App\Models\UserFactorModel;
use App\Services\Dog\DogUserService;

/**
 * 大黄失效检测
 * Class FactorEventSubscriber
 * @package App\Listeners
 */
class UserDogFailureEventSubscriber
{


    public function failure(UserDogFailureEvent $event)
    {
        $userDogService = new DogUserService();
        $userDogService->testing($event->userDog);
    }


    public function subscribe($events)
    {
        $events->listen(
            UserDogFailureEvent::class,
            'App\Listeners\UserDogFailureEventSubscriber@failure'
        );
    }
}
