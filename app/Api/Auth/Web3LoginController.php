<?php

namespace App\Api\Auth;

use App\Api\Controller;
use App\Services\IdentityService;
use App\Support\Response;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use M1guelpf\Web3Login\Facades\Signature;

class Web3LoginController extends Controller
{
    public function signature()
    {
        $nonce = Str::random();

        Redis::setex('nonce:' . $nonce, 86400, $nonce);

        return Response::success([
            'nonce' => $nonce,
            'message' => Signature::generate($nonce),
        ]);
    }

    public function login(Request $request)
    {
        try {
            $payload = IdentityService::authenticate('web3', $request->address);

            return Response::success($payload);
        } catch (Exception $th) {
            Log::error($th->getMessage() . $th->getLine());
            if ($th->getCode() == 1235) {
                return Response::error($th->getMessage());
            }

            return Response::error(trans('app-return.error_msg'));
        }
    }
}
