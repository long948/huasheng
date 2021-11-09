<?php


namespace App\Jobs;


use App\Models\Dog\DogUser;
use App\Models\User\MinerUserSaplingTotalRelease;
use App\Services\Dog\DogUserService;
use App\Services\User\MinerUserSaplingTotalReleaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserIncomeJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $userId;


    /**
     * UserIncomeJob constructor.
     * @param string $userId
     */
    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }


    public function handle()
    {
        echo "用户编号:{$this->userId}的开始往仓库累计花生米\n";
        $userSaplingTotalReleaseService = new MinerUserSaplingTotalReleaseService();
        $userSaplingTotalReleaseService->cumulativeIncome($this->userId);
    }

}
