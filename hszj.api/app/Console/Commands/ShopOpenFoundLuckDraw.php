<?php


namespace App\Console\Commands;


use App\Services\Shop\ShopTeamFoundService;
use Illuminate\Console\Command;

class ShopOpenFoundLuckDraw extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:order:luck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拼购订单抽奖';

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
     * @param ShopTeamFoundService $teamFoundService
     */
    public function handle(ShopTeamFoundService $teamFoundService)
    {
        try {
            $followOrder = $teamFoundService->getTimeOutOrder();
            foreach ($followOrder as $item) {
                $result = $teamFoundService->openFoundLuckDraw($item->found_id);
            }
        } catch (\Exception $e) {
            echo '开团抽奖出现错误:' . $e->getMessage();
        }
    }


}
