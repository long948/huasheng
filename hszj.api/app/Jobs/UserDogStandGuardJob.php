<?php


namespace App\Jobs;


use App\Models\Dog\DogUser;
use App\Services\Dog\DogUserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Psr\SimpleCache\InvalidArgumentException;

class UserDogStandGuardJob implements ShouldQueue
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
     * 设置大黄上岗后下岗
     * @return void
     * @throws \Throwable
     * @throws InvalidArgumentException
     */
    public function handle()
    {
        echo "用户编号:{$this->userDog->user_id}的大黄编号:{$this->userDog->id}已下岗\n";
        $dogUserService = new DogUserService();
        $dogUserService->standGuardDown($this->userDog);
    }
}
