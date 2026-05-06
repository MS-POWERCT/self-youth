<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class LastOnlineAt
{

    public function handle($request, Closure $next)
    {

        // 缓存各接口的请求量到redis 的hash中
        $routeName = $request->route()?->uri() ?? 'unknown';
        $routeKey = 'api_pv:route_' . date('Ymd');
        Redis::hincrby($routeKey, $routeName, 1);

        if (auth()->guest()) {
            return $next($request);
        }
        $user = auth()->user();
        // 如果是登录后进行的操作，更新last_online_at
        if ($user->status == 1) {
            return response()->json(array('res_code' => 418, 'res_msg' => trans('app-return.user_cease'), 'data' => []));
        }

        // 限制用户每秒请求次数
        $cache_name = 'online:' . $user->id;
        if (!Cache::has($cache_name)) {
            Cache::put($cache_name, $user->id, rand(300, 500));

            DB::table("users")->where("id", $user->id)->update(["last_online_at" => now(), 'ip' => $GLOBALS['clientIp']]);
        }

        return $next($request);
    }
}
