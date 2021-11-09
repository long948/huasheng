<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ArException;
use App\Models\CoinModel as Coin;
use App\Models\FinancingMoldModel as FinancingMold;
use App\Models\MemberCoinModel as MemberCoin;

class CTCResetOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ctc:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重置挂单';

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
        $result = DB::table('CTCOrder')->where('State',0)->update(['State' => 1]);
        echo ($result ? 'success' : 'error');
    }


}
