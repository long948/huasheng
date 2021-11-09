<?php

namespace App\Http\Middleware;

use App\Libraries\Send;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemClose
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $system_is_close = DB::table('setting')->where('k', 'system_is_close')->value('v');
        $system_close_explanation = DB::table('setting')->where('k', 'system_close_explanation')->value('v');
        if ($system_is_close) {
            Send::returnMsg($system_close_explanation, 100000, []);
        }
        return $next($request);
    }
}
