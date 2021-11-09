<?php

namespace App\Console\Commands;

use App\Services\MinerDividendService;
use Illuminate\Console\Command;

/**
 * 分红与收益
 * Class UserDividend
 * @package App\Console\Commands
 */
class UserDividend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:dividend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '分红与收益';

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
        $minDividendService = new MinerDividendService();
        //奖励树苗
        //$minDividendService->rewardUserSapling();
        //分红收益
        $minDividendService->userDividend();
    }
}
