<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * 购买树苗分享奖励后的释放
 * Class SaplingEvent
 * @package App\Events
 */
class SaplingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * 用户编号
     * @var int
     */
    public $user_id;

    /**
     * Create a new event instance.
     *
     * @param $user_id
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

}
