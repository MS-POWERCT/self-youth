<?php

namespace App\Api\Auth;

use App\Api\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Support\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use M1guelpf\Web3Login\Facades\Signature;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Web3LoginController extends Controller
{
    public function signature()
    {
        $nonce = Str::random();

        Cache::setex('nonce:' . $nonce, $nonce, 86400);

        return Response::success($nonce, Signature::generate($nonce));
    }

    // 矿池 web3登录
    public function login(Request $request)
    {

        try {
            $address = $request->address;
            $user = User::where('address', $request->address)->where('login_type', 'address')->first();

            if (!$user) {
                DB::beginTransaction();
                $user = UserService::createUser($address, 'address');
                DB::commit();
            }

            if ($user->status != 1) {
                $user->tokens()->delete();
                $access_token = $user->createToken('api')->accessToken;
            } else {
                throw new Exception(trans('app-return.acount_not_exist'), 1235);
            }

            return Response::success([
                'res_code' => 0,
                'res_msg' => trans('app-return.welcome_msg'),
                'access_token' => $access_token
            ]);
        } catch (Exception $th) {
            DB::rollBack();
            Log::error($th->getMessage() . $th->getLine());
            if ($th->getCode() == 1235) {
                return Response::error($th->getMessage());
            }
            return Response::error(trans('app-return.error_msg'));
        }
    }
}
