<?php

namespace App\Http\Middleware;

use App\Services\EmailLoginService;
use App\Support\Response;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailLoginCodeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.env') === 'local') {
            return $next($request);
        }

        try {
            EmailLoginService::checkLoginCode($request);
        } catch (Exception $th) {
            Log::error('Email Login Code Verification Failed', [
                'line' => $th->getLine(),
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
                'file' => $th->getFile(),
                'request' => [
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'email' => $request->input('email'),
                ],
            ]);

            $code = $th->getCode() ?: 5001;

            return Response::error($th->getMessage(), $code);
        }

        return $next($request);
    }
}
