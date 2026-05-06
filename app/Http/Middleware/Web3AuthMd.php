<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\Response;
use Illuminate\Http\Request;
use Closure;

class Web3AuthMd
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * 区分多运营商-中间件
     *
     */
    public function handle(Request $request, Closure $next)
    {
        if (empty($address = $request->address)) {
            return Response::error('Address parameter is required', 400, 400);
        }
        $user = User::where('address', $address)->first();

        // 检查用户是否存在
        if (!$user) {
            return Response::error(trans('app-return.handler.referral_code_not_bound'), 401, 401);
        }

        $GLOBALS['web3_user'] = $user;

        return $next($request);
    }
}
