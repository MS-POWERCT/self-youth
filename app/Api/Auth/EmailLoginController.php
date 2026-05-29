<?php

namespace App\Api\Auth;

use App\Api\Controller;
use App\Models\User;
use App\Services\ToolsService;
use App\Services\UserService;
use App\Support\Response;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmailLoginController extends Controller
{

    // 发验证码
    // 目前有发送的类型
    // 1.邮箱登录
    // 2.设置密码/重置密码
    // 3.邮箱绑定地址
    // 4.地址绑定邮箱
    public function sendEmailCode(Request $request)
    {
        // 1. 校验邮箱格式
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string'],
            'category' => ['required', Rule::in(['login', 'recover', 'bind-email', 'bind-address'])],
        ]);

        if ($validator->fails()) {
            return Response::error('邮箱格式不正确', 5001);
        }


        $email = $request->email;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(array('res_code' => 5003, 'res_msg' => trans('app-return.email_format_error'), 'data' => []));
        }


        $category = $request->category;
        $cache_key = UserService::getEmailCodeKey($email, $category);
        $cooling_time = ToolsService::getCache('EMAIL_CODE_COOLING_TIME');
        $time = ToolsService::getCache('EMAIL_CODE_TIME') ?? 300;

        // 2. 冷却检查：60秒内不能重复发送
        $limitKey = UserService::getEmailCodeLimitKey($email, $category);
        if (Redis::exists($limitKey)) {
            return Response::error("操作频繁，请{$cooling_time}秒后再试", 5002);
        }

        // 3. 生成6位验证码
        $code = rand(100000, 999999);


        // 4. 存入Redis，5分钟过期
        Redis::setex($cache_key, $time, $code);
        Redis::setex($limitKey, $cooling_time, 1);

        // 5. 发送邮件
        Mail::send('emails.code', ['code' => $code, 'url' => config('app.url'), 'time' => $time], function ($message) use ($email) {
            $message->to($email)->subject('[' . config('app.name') . '] ' . 'Verification - ' . date('Y-m-d H:i:s'));
        });

        return Response::success([], '发送成功');
    }



    // 验证码登录
    public function loginEmail(Request $request)
    {
        // 1. 校验邮箱格式
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string'],
            'code' => ['required', 'integer']
        ]);

        if ($validator->fails()) {
            return Response::error('邮箱格式不正确', 5001);
        }


        $email = $request->email;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(array('res_code' => 5003, 'res_msg' => trans('app-return.email_format_error'), 'data' => []));
        }
        // 校验验证码
        if (config('app.env') == 'production') {
            if (!UserService::checkEmailCode($email, 'login', $request->code)) {
                return Response::error('验证码错误或已过期', '5001');
            }
        }

        try {
            $user = User::where('email', $email)->where('login_type', 'email')->first();

            if (!$user) {
                DB::beginTransaction();
                $user = UserService::createUser($email, 'email');
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
