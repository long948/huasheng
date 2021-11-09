<?php

namespace App\Providers;

use App\Models\AdminUserModel;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Observer\AdminLogObserver;
use Observer\AdminUserObserver;

class AppServiceProvider extends ServiceProvider
{

    public function boot()
    {
//        AdminUserModel::observe(AdminUserObserver::class);//注册AdminUser模型观察者
//        BaseModel::observe(AdminLogObserver::class);
        DB::listen(function ($query) {
            $tmp = str_replace('?', '"' . '%s' . '"', $query->sql);
            $qBindings = [];
            foreach ($query->bindings as $key => $value) {
                if (is_numeric($key)) {
                    $qBindings[] = $value;
                } else {
                    $tmp = str_replace(':' . $key, '"' . $value . '"', $tmp);
                }
            }
            $tmp = vsprintf($tmp, $qBindings);
            $tmp = str_replace("\\", "", $tmp);
            Log::info(' execution time: ' . $query->time . 'ms; ' . $tmp . "\n\n\t");
        });
    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}
