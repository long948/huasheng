<?php

namespace App\Console\Commands;

use App\Services\User\UserTeamService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserTeam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:team';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '用户团队信息统计';

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
        try {
            $userTeamService = new UserTeamService();
            DB::table('Members')->select(['id'])->orderBy('id')
                ->chunk(100, function ($list) use ($userTeamService) {
                    foreach ($list as $item) {
                        $userTeamService->exchange_people_count($item->id);
                        $userTeamService->total_people_count($item->id);
                        $userTeamService->self_computing_power($item->id);
                        $userTeamService->reward($item->id);
                    }
                });
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }
}
