<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancleOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cancle:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动取消订单';

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
     */
    public function handle()
    {
        //
        $setting = DB::table('PlatformSetting')->first();
        if(empty($setting)) exit("平台未设置自动取消时间");
        $time = time() - $setting->TradeCancleTime * 60;
        swoole_timer_tick(2000, function ($timer_id) use ($time){
            $res = DB::table('Trade')->where('Type', 1)->where('State', 1)->where('AddTime','<', $time)->update(['State' => 4]);
        });
    }
}
