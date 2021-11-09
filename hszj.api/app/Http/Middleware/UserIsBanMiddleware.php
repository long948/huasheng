<?php

namespace App\Http\Middleware;

use App\Exceptions\ArException;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * 检测用户是否被禁用
 * Class UserIsBanMiddleware
 * @package App\Http\Middleware
 */
class UserIsBanMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws ArException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle($request, Closure $next)
    {
        $uid = $request->get('uid');
        if (!empty($uid)) {
            $key = "user:is_ban:{$uid}";
            $isBan = 0;
            if (Cache::has($key)) {
                $isBan = Cache::get($key);
            } else {
                $isBan = DB::table('Members')->find($uid)->value('isBan');
                Cache::set($key, $isBan, 7200);
            }
            if ($isBan) {
                throw new ArException(ArException::USER_BE_BAN);
            }
        }
        return $next($request);
    }
}
