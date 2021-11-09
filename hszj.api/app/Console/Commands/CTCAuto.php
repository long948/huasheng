<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ArException;
use App\Models\CoinModel as Coin;
use App\Models\FinancingMoldModel as FinancingMold;
use App\Models\MemberCoinModel as MemberCoin;
use App\Services\CtcService;

class CTCAuto extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'ctc:auto';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = '超时自动处理';

        /**
         * Create a new command instance.
         *
         * @return void
         */
        public function __construct(CtcService $ctcService)
        {
            parent::__construct();

            $this->ctcService = $ctcService;
        }

        /**
         * Execute the console command.
         *
         * @return mixed
         */
        public function handle()
        {
            bcscale(10);
            $setting = DB::table('CTCSetting')->first();
            if (empty($setting)) exit("未配置自动取消");
            $cancleTime = $setting->CancleTime;
            $time = time();
            DB::beginTransaction();
            try {
                $trades = DB::table('CTCTrade')
                    ->whereRaw("Type = 2 and ({$time}-AddTime >= $cancleTime and State = 0) or ({$time}-PayTime >= $cancleTime and State = 1)")
                    ->get();
                foreach ($trades as $trade) {
                    if ($trade->State == 1) {//待确认 自动确认&冻结求购者
                        //自动确认
                        $this->ctcService->Confirm($trade->MemberId,$trade->Id);

                        DB::table('Members')
                            ->where('Id', $trade->MemberId)
                            ->update([
                                'IsFrozenCTC' => 1
                            ]);
                    }
                    if ($trade->State == 0) {//待支付过期&冻结出售者
                        DB::table('CTCTrade')
                            ->where('Id', $trade->Id)
                            ->where('State', $trade->State)
                            ->update([
                                'State' =>  4,
                                'FinishTime' => time()
                            ]);

                        $unforzen = bcadd($trade->Number,$trade->Fee);
                        DB::table('MemberCoin')->where('MemberId', $trade->MemberId)->where('CoinId', $trade->CoinId)->update([
                            'Money' => DB::raw("Money+{$unforzen}"),
                            'Forzen' => DB::raw("Forzen-{$unforzen}")
                        ]);
                        $coin = Coin::where('Id', $trade->CoinId)->first();
                        self::AddLog($trade->MemberId, $unforzen, $coin, 'ctc_sell');

                        DB::table('Members')
                            ->where('Id', $trade->OrderMemberId)
                            ->update([
                                'IsFrozenCTC' => 1
                            ]);
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                var_dump($e->getLine());
                var_dump($e->getMessage());
            }
        }

        protected static function AddLog(int $uid, $money, Coin $coin, string $mold){
            $sort = $uid % 20;
            if($sort < 10) $sort = '0'.$sort;
            $table = 'FinancingList_'.$sort;
            $fina = FinancingMold::where('call_index', $mold)->first();
            if(empty($fina)) throw new ArException(ArException::SELF_ERROR, 'mold '.$mold.' not found');
            $memberCoin = MemberCoin::where('MemberId', $uid)->where('CoinId', $coin->Id)->first();
            if(empty($memberCoin)) throw new ArException(ArException::UNKONW);
            if(bccomp($memberCoin->Money, 0, 10) < 0) throw new ArException(ArException::COIN_NOT_ENOUGH);
            $data = [
                'MemberId' => $uid,
                'Money' => $money,
                'CoinId' => $coin->Id,
                'CoinName' => $coin->EnName,
                'Mold' => $fina->id,
                'MoldTitle' => $fina->title,
                'Remark' => $fina->title,
                'AddTime' => time(),
                'Balance' => $memberCoin->Money
            ];
            DB::table($table)->insert($data);
        }
    }
