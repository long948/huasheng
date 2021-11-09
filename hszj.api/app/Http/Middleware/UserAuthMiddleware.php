<?php

namespace App\Http\Middleware;

use App\Exceptions\ArException;
use Closure;
use Illuminate\Http\Request;

/**
 * 实名认证检测中间件
 * Class UserAuthMiddleware
 * @package App\Http\Middleware
 */
class UserAuthMiddleware
{

    /**
     * Handle an incoming request.
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws ArException
     */
    public function handle($request, Closure $next)
    {
        $userId = $request->get('uid');
        $isAuth = userIsAuth($userId);
        if (!$isAuth) {
            throw new ArException(ArException::SELF_ERROR, '请先实名认证');
        }
        return $next($request);
    }
}
