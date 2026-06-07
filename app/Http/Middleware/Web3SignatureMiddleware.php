<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use App\Services\Web3LoginService;
use App\Support\Response;
use Illuminate\Support\Facades\Log;

class Web3SignatureMiddleware
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
        if (config('app.env') == 'local') {
            // 本地不需要签名
            return $next($request);
        }
        try {
            Web3LoginService::checkSignature($request);
        } catch (Exception $th) {
            Log::error('Web3 Signature Verification Failed', [
                'line' => $th->getLine(),
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
                'file' => $th->getFile(),
                'request' => [
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'input' => $request->all(),
                ]
            ]);
            return Response::error('Web3 Signature Verification Failed');
        }

        return $next($request);
    }
}
