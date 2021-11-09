<?php


namespace App\Events;

use App\Models\Dog\DogUser;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * 检测大黄是否到期
 * Class UserDogFailureEvent
 * @package App\Events
 */
class UserDogFailureEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * @var DogUser
     */
    public $userDog;

    /**
     * UserDogFailureEvent constructor.
     * @param DogUser $userDog
     */
    public function __construct(DogUser $userDog)
    {
        $this->userDog = $userDog;
    }

}
