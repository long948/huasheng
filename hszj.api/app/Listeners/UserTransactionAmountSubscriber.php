<?php


namespace App\Listeners;


use App\Events\UserTransactionAmountEvent;
use App\Services\UserGiveAwayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserTransactionAmountSubscriber
{

    /**
     * 累计
     * @param UserTransactionAmountEvent $event
     * @return bool
     * @throws \Throwable
     */
    public function grandTotal(UserTransactionAmountEvent $event)
    {
        try {
            $isPrincipal = getUserIsPrincipal($event->getUserId());
            if ($isPrincipal) {
                return false;
            }
            $user = DB::table('Members')->where('id', $event->getUserId())->select(['id', 'transaction_amount', 'is_principal'])->first();
            $principal = DB::table('setting')->where('k', 'principal')->value('v') ?? 0;

            if ($user->transaction_amount + $event->getAmount() >= $principal) {
                DB::table('Members')->where('id', $event->getUserId())->update([
                    'transaction_amount' => DB::raw("transaction_amount + {$event->getAmount()}"),
                    'is_principal' => 1
                ]);
                $userGiveService = new UserGiveAwayService();
                $userGiveService->transfer($event->getUserId());
                return true;
            }

            DB::table('Members')->where('id', $event->getUserId())->update([
                'transaction_amount' => DB::raw("transaction_amount + {$event->getAmount()}")
            ]);
        } catch (\Exception $e) {
            Log::error('转出赠送金额出错，具体原因：' . $e->getMessage());
        }
    }

    public function subscribe($events)
    {
        $events->listen(
            UserTransactionAmountEvent::class,
            'App\Listeners\UserTransactionAmountSubscriber@grandTotal'
        );
    }

}
