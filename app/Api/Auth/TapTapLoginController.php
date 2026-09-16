<?php

namespace App\Api\Auth;

use App\Api\Controller;
use App\Services\TapTapLoginService;
use App\Support\Response;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TapTapLoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $payload = TapTapLoginService::login($request);

            return Response::success($payload);
        } catch (Exception $th) {
            Log::error('TapTap Login Failed', [
                'line' => $th->getLine(),
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
            ]);

            if ($th->getCode() == 1235) {
                return Response::error($th->getMessage());
            }

            if ($th->getCode() >= 6301 && $th->getCode() <= 6304) {
                return Response::error($th->getMessage(), $th->getCode());
            }

            return Response::error(trans('app-return.error_msg'));
        }
    }
}
