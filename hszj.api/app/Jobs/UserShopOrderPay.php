<?php


namespace App\Jobs;


use App\Models\Dog\DogUser;
use App\Models\Shop\TeamFound;
use App\Services\Dog\DogUserService;
use App\Services\Shop\ShopTeamFollowService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserShopOrderPay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @var int
     */
    private $userId;

    /**
     * @var TeamFound
     */
    private $found;

    /**
     * @var string
     */
    private $followId;

    /**
     * UserDogRestJob constructor.
     * @param int $userId
     * @param TeamFound $found
     * @param string $followId
     */
    public function __construct(int $userId, TeamFound $found, string $followId)
    {
        $this->userId = $userId;
        $this->found = $found;
        $this->followId = $followId;
    }


    /**
     * @return void
     * @throws \Throwable
     */
    public function handle()
    {
        try {
            $out = "开始处理用户编号:{$this->userId},参团主编号:{$this->found->found_id},参团编号:{$this->followId}";
            Log::info($out);
            echo $out . "\n";
            $followService = new ShopTeamFollowService();
            $result = $followService->followNotify($this->followId);
        } catch (\Exception $e) {
            Log::error('处理出现错误:' . $e->getMessage());
        }
    }

}
