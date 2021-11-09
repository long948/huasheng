<?php

namespace App\Console\Commands;

use App\Services\MinerUserLevelService;
use App\Services\Other\UserConventionalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:level';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '用户等级升级';


    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function handle()
    {
        $userLevelService = new MinerUserLevelService();

//        $userConventionalService = new UserConventionalService();
//        $userConventionalService->changeUserLevel($sp->id);

        DB::table('members')->select(['id'])->orderBy('id', 'desc')->chunk(100, function ($users)
        use ($userLevelService) {
            foreach ($users as $sp) {
                echo now() . "-开始更新{$sp->id}编号的用户等级...\n";
                $userLevelService->settingUserLevel($sp->id);
            }
        });
    }
}
