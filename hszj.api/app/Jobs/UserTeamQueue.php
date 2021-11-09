<?php

namespace App\Jobs;

use App\Services\MinerSaplingShareRewardService;
use App\Services\User\UserTeamService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserTeamQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user_id;

    /**
     * UserTeamRequest constructor.
     * @param $user_id
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Throwable
     */
    public function handle()
    {
        try {
            
            (new UserTeamService())->reward($this->user_id);
            (new MinerSaplingShareRewardService())->userReward($this->user_id);
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }
}
