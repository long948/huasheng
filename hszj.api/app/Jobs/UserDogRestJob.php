<?php


namespace App\Jobs;


use App\Models\Dog\DogUser;
use App\Services\Dog\DogUserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserDogRestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @var DogUser
     */
    private $userDog;

    /**
     * UserDogRestJob constructor.
     * @param DogUser $userDog
     */
    public function __construct(DogUser $userDog)
    {
        $this->userDog = $userDog;
    }


    /**
     * 处理休息完后上岗
     * @return void
     * @throws \Throwable
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle()
    {
        echo "用户编号:{$this->userDog->user_id}的大黄编号:{$this->userDog->id}已休息完成\n";
        $dogUserService = new DogUserService();
        $dogUserService->finishTheRest($this->userDog);
    }

}
