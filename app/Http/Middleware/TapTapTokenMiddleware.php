<?php

namespace App\Http\Middleware;

use App\Services\TapTapLoginService;
use App\Support\Response;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TapTapTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.env') === 'local') {
            return $next($request);
        }

        try {
            TapTapLoginService::validateTokenParams($request);
            TapTapLoginService::ensureConfigured();
        } catch (Exception $th) {
            Log::error('TapTap Token Verification Failed', [
                'line' => $th->getLine(),
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
                'file' => $th->getFile(),
                'request' => [
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                ],
            ]);

            $code = $th->getCode() ?: 6302;

            return Response::error($th->getMessage(), $code);
        }

        return $next($request);
    }
}
