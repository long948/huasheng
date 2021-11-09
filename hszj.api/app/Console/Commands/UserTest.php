<?php

namespace App\Console\Commands;

use App\Services\MinerUserLevelService;
use App\Services\MinerUserSaplingService;
use App\Services\Other\UserConventionalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试命令';


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
        $service = new MinerUserSaplingService();
        $data = [
            ['uid' => 1, 'id' => '211176054860089345'],
            ['uid' => 1, 'id' => '211176054860089344'],
            ['uid' => 1001, 'id' => '211176054860089344'],
            ['uid' => 1001, 'id' => '211176054860089345'],
            ['uid' => 1004, 'id' => '211176054860089344'],
            ['uid' => 1011, 'id' => '211176054860089344'],
            ['uid' => 1035, 'id' => '211176054860089344'],
            ['uid' => 1039, 'id' => '211176054860089344'],
            ['uid' => 4970, 'id' => '211176054860089344'],
        ];
        foreach ($data as $it) {
            $service->giveAway($it['uid'], $it['id'], 6, '升级赠送');
        }
    }
}
