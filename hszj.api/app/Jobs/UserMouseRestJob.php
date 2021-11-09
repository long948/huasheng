<?php


namespace App\Jobs;


use App\Models\Dog\DogUser;
use App\Models\Mouse\MouseUser;
use App\Services\Dog\DogUserService;
use App\Services\Mouse\MouseUserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserMouseRestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @var $userMouse MouseUser
     */
    private $userMouse;

    /**
     * UserMouseRestJob constructor.
     * @param MouseUser $userMouse
     */
    public function __construct(MouseUser $userMouse)
    {
        $this->userMouse = $userMouse;
    }


    /**
     * 疗伤完成后恢复正常
     * @return void
     * @throws \Throwable
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle()
    {
        $mouseUserService = new MouseUserService();
        $mouseUserService->restoreMouseHealing($this->userMouse);
    }
}
