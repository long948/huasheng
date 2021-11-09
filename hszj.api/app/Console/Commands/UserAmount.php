<?php

namespace App\Console\Commands;

use App\Models\User\UserTotalIncome;
use App\Services\User\UserTotalIncomeService;
use Illuminate\Console\Command;

class UserAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:amount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '用户花生米结算';

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
        $userTotalIncomeService = new UserTotalIncomeService();
        $userIncomeList = UserTotalIncome::query()->where('amount', '>', 0)->select(['id', 'user_id', 'amount'])->get();
        try {
            foreach ($userIncomeList as $item) {
                /**
                 * @var $item UserTotalIncome
                 */
                echo "开始用户编号为:{$item->user_id}转移仓库花生米\n";
                $userId = $item->user_id;
                $userTotalIncomeService->transfer($item->user_id, $item->amount);
            }
        } catch (\Exception $e) {
            echo "转移用户编号为：{$userId}错误错误，具体原因：{$e->getMessage()}";
        }
    }
}
