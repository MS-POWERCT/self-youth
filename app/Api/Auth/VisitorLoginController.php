<?php

namespace App\Api\Auth;

use App\Api\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Support\Response;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VisitorLoginController extends Controller
{


    // 验证码登录
    public function loginVisitor(Request $request)
    {
        // 1. 校验邮箱格式
        $validator = Validator::make($request->all(), [
            'uuid' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return Response::error('格式不正确', 5001);
        }


        $uuid = $request->uuid;



        try {
            $user = User::where('uuid', $uuid)->where('login_type', 'uuid')->first();

            if (!$user) {
                DB::beginTransaction();
                $user = UserService::createUser($uuid, 'uuid');
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
