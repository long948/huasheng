<?php


namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * 注册事件
 * Class RegisterEvent
 * @package App\Event
 */
class UserRegisterEvent
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * 用户编号
     * @var String
     */
    public $userId;

    public function __construct(String $userId)
    {
        $this->userId = $userId;
    }


    /**
     * @return String
     */
    public function getUserId(): String
    {
        return $this->userId;
    }

}
