<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Support\Response;

class CheckUUIDVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 获取当前认证用户
        $user = auth()->user();

        // 检查用户是否存在且已认证
        if (! $user) {
            return Response::error(trans('app-return.handler.login_in'), 401, 401);
        }
        // 检查用户是否为游客状态
        // 游客状态：uuid有值，但email和address都为空
        if (!empty($user->uuid) && empty($user->email) && empty($user->address)) {
            return Response::error('请先绑定邮箱或钱包地址', 6200);
        }

        return $next($request);
    }
}
