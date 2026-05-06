<?php

namespace App\Http\Middleware;

use App\Support\Response;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/**
 * 防止表单重复提交
 * Class LimitFormRepeat
 * @package App\Http\Middleware
 */
class LimitFormRepeat
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param null $cache_name
     * @return mixed
     */
    public function handle($request, Closure $next, $ttl = 3)
    {

        $md5_str = Route::currentRouteAction() . $GLOBALS['clientIp'] . $request->address;
        $cache_name = md5($md5_str);

        if (Cache::has($cache_name)) {
            return Response::error(trans('app-return.handler.again_later'), 429, 429);
        }

        Cache::put($cache_name, $md5_str, $ttl);

        return $next($request);
    }
}
