<?php

namespace App\Console\Commands;

use App\Services\MinerUserSaplingService;
use App\Services\System\MembersSignService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserSapling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:sapling';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '花田产生花生米';

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
        $userSaplingService = new MinerUserSaplingService();
        $memberSignService = new MembersSignService();
        DB::table('miner_user_sapling')
            ->where('freed', '<', 1)
            ->where('surplus_amount', '<=', 0)
            ->where('is_delete', '!=', 1)
            ->update([
                'is_release_complete' => 1,
                'is_disable' => 1,
                'is_delete' => 1,
                'release_complete_time' => dateFormat()
            ]);
        
        DB::table('members')->select(['id'])->orderBy('id')->chunk(100, function ($users)
        use ($userSaplingService, $memberSignService) {
            foreach ($users as $user) {
                //if ($memberSignService->yesterdayIsSign($user->id)) {
                $userSaplingService->dayUserSaplingRelease($user->id);
                //}
            }
        });

    }
}
