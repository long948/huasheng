<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoConfirm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:confirm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动确认';

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

        $setting = DB::table('PlatformSetting')->first();
        if(empty($setting)) exit("平台未设置自动确认时间");
        $time = time() - $setting->AutoConfirm * 3600;
        DB::beginTransaction();
        try{
            $rush = DB::table('Trade')->where('Type', 2)->where('State', 2)->where('PayTime','<',$time)->paginate(1000);
            foreach($rush as $item){
                //扣掉冻结的余额
                DB::table('MemberCoin')->where('CoinId', $item->CoinId)->where('MemberId', $item->MemberId)->decrement('Forzen', $item->Number);
                //
                DB::table('Trade')->where('Id', $item->Id)->update([
                    'FinishTime' => time(),
                    'State' => 3
                ]);
            }
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            var_dump($e->getMessage());
            var_dump($e->getLine());
        }
    }
}
