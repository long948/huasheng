<?php

namespace App\Providers;

use App\Models\AdminUserModel;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;
use Observer\AdminUserObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\ExampleEvent' => [
            'App\Listeners\ExampleListener',
        ],
    ];

    public function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
//        AdminUserModel::updated(function (AdminUserModel $user) {
//            dd(11);
//        });

    }
}
