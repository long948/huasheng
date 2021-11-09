<?php


namespace App\Console\Commands;


use App\Services\Shop\ShopTeamFollowService;
use Illuminate\Console\Command;

class ShopFollowTimeOut extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:order:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拼购订单超时取消';

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
     * @param ShopTeamFollowService $teamFollowService
     */
    public function handle(ShopTeamFollowService $teamFollowService)
    {
        try {
            $followOrder = $teamFollowService->getTimeOutOrder();
            foreach ($followOrder as $item) {
                $result = $teamFollowService->followTimeOutCancel($item->follow_user_id, $item->follow_id);
            }
        } catch (\Exception $e) {
            echo '取消参团订单出现错误：' . $e->getMessage();
        }
    }

}
