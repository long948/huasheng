<?php

namespace App\Console\Commands;

use App\Services\MinerUserSaplingService;
use App\Services\User\UserTeamInfoExtendService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class UserTotal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:total';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '用户仓库转移';

    /**
     * Create a new command instance.
     *
     * @return void
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
        $list = DB::table('members')->select(['Id'])->get();
        $userInfoService = new UserTeamInfoExtendService();
        foreach ($list as $item) {
            $userInfoService->init($item->Id);
            $count = DB::table('miner_user_sapling')->where('user_id', $item->Id)->whereIn('type', [2, 3, 4])->count('id');
            $count = $count > 1 ? 1 : 0;
            $userInfoService->update($item->Id, $count, $count);
        }
    }
}
