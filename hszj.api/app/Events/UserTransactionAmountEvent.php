<?php


namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * 交易转账事件
 * Class RegisterEvent
 * @package App\Event
 */
class UserTransactionAmountEvent
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * 用户编号
     * @var String
     */
    private $userId;

    /**
     * 金额
     * @var string
     */
    private $amount;

    /**
     * UserTransactionAmountEvent constructor.
     * @param String $userId
     * @param string $amount
     */
    public function __construct(String $userId, string $amount)
    {
        $this->userId = $userId;
        $this->amount = $amount;
    }

    /**
     * @return String
     */
    public function getUserId(): String
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

}
