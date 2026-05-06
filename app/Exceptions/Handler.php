<?php

namespace App\Exceptions;

use App\Support\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Exceptions\OAuthServerException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        // $this->reportable(function (Throwable $e) {
        //     //
        // });

        $this->reportable(function (\League\OAuth2\Server\Exception\OAuthServerException $e) {
            if ($e->getCode() == 9)
                return false;
        });
    }
    public function report(Throwable $exception)
    {
        // 如果异常类型为 OAuthServerException，则不打印日志
        if ($exception instanceof OAuthServerException) {
            return;
        }
    }


    public function render($request, Throwable $exception)
    {
        // if ($exception instanceof QueryException) {
        //     $transactionId = DB::getPdo()->inTransaction() ? DB::getPdo()->getAttribute(\PDO::ATTR_SERVER_INFO) : null;
        //     Log::error('Transaction ID: ' . $transactionId);
        //     Log::error('Exception: ' . $exception->getMessage());
        //     Log::error('SQL: ' . $exception->getSql());
        //     Log::error('Bindings: ' . json_encode($exception->getBindings()));
        // }


        if ($exception instanceof OAuthServerException && $exception->getCode() === 6) {
            // 自定义错误消息
            return Response::error(trans('app-return.handler.user_login_error'), $exception->statusCode());
            // return response()->json(array(
            //     'res_code' => $exception->statusCode(),
            //     'res_msg' => trans('app-return.handler.user_login_error'),
            //     'message' => trans('app-return.handler.user_login_error'),
            // ), $exception->statusCode());
        }

        // 这个是自己定的,判断账户是否存在
        if ($exception instanceof OAuthServerException && $exception->getCode() === 99) {
            // 自定义错误消息
            return Response::error($exception->getMessage(), $exception->statusCode());
            // return response()->json(array(
            //     'res_code' => $exception->statusCode(),
            //     'res_msg' => $exception->getMessage(),
            //     'message' => $exception->getMessage(),
            // ), $exception->statusCode());
        }
        // if ($exception instanceof OAuthServerException && $exception->getCode() === 89) {
        //     return response()->json(array(
        //         'res_code' => $exception->getCode(),
        //         'res_msg' => $exception->getMessage(),
        //         'message' => $exception->getMessage()
        //     ), $exception->statusCode());
        // }

        if ($exception instanceof ThrottleRequestsException) {
            // 自定义错误消息
            return Response::error(trans('app-return.handler.again_later'), 429, 429);
            // return response()->json(array(
            //     'res_code' => 429,
            //     'res_msg' =>  trans('app-return.handler.again_later'),
            //     'message' => trans('app-return.handler.again_later')
            // ), 429);
        }


        parent::report($exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return Response::error(trans('app-return.handler.login_in'), 401, 401);
        }
    }
}
