<?php

namespace App\Console\Commands;

use App\Models\CoinModel as Coin;
use App\Services\MinerUserLevelService;
use App\Services\Other\UserConventionalService;
use App\Services\User\UserPartnerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * 合伙人分红
 * Class UserPartner
 * @package App\Console\Commands
 */
class UserPartner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:partner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '合伙人分红';


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
     * @param UserPartnerService $service
     * @return mixed
     * @throws \App\Exceptions\ArException
     */
    public function handle(UserPartnerService $service)
    {
        $dividend = DB::table('member_partner_dividend')->where('is_hande', 0)->orderByDesc('create_time')->first();
        if (empty($dividend)) {
            return;
        }

        $user = DB::table('members')->select(['id'])->where('isPartner', 1)->orderBy('id', 'desc')->get();
        if ($user->isEmpty()) {
            return;
        }

        $num = count($user);
        $amount = bcdiv($dividend->total_amount, $num, 2);

        DB::table('member_partner_dividend')->where('id',$dividend->id)->update([
            'amount' =>$amount,
            'num'=>$num,
            'is_hande'=>1,
        ]);
        foreach ($user as $item) {
            $service->partner($item->id, $amount);
        }

    }
}
