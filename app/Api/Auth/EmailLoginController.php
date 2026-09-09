<?php

namespace App\Api\Auth;

use App\Api\Controller;
use App\Models\UserIdentity;
use App\Services\IdentityService;
use App\Services\ToolsService;
use App\Services\UserService;
use App\Support\Response;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmailLoginController extends Controller
{
    public function sendEmailCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string'],
            'category' => ['required', Rule::in(['login', 'recover', 'bind_email'])],
        ]);

        if ($validator->fails()) {
            return Response::error('邮箱格式不正确', 5001);
        }

        $email = strtolower(trim($request->email));
        $category = $request->category;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'res_code' => 5003,
                'res_msg' => trans('app-return.email_format_error'),
                'data' => [],
            ]);
        }

        if ($category == 'bind_email' && IdentityService::identityExists(UserIdentity::PROVIDER_EMAIL, $email)) {
            return Response::error('邮箱已绑定,请更换其他邮箱', '5001');
        }

        $cache_key = UserService::getEmailCodeKey($email, $category);
        $cooling_time = ToolsService::getCache('EMAIL_CODE_COOLING_TIME');
        $time = ToolsService::getCache('EMAIL_CODE_TIME') ?? 300;

        $limitKey = UserService::getEmailCodeLimitKey($email, $category);
        if (Redis::exists($limitKey)) {
            return Response::error("操作频繁，请{$cooling_time}秒后再试", 5002);
        }

        $code = rand(100000, 999999);

        Redis::setex($cache_key, $time, $code);
        Redis::setex($limitKey, $cooling_time, 1);

        $categoryMap = [
            'login' => '邮箱登录',
            'recover' => '重置密码',
            'bind_email' => '绑定邮箱',
        ];
        $category_text = $categoryMap[$category] ?? '验证';
        $time_minutes = round($time / 60);

        $app_name = $request->header('app_name');
        $email_view = $app_name == 'MyFarm' ? 'emails.farm_code' : 'emails.new_code';
        $title = '[' . $app_name . '] Verification Code';

        Mail::send($email_view, [
            'code' => $code,
            'time' => $time_minutes,
            'url' => config('app.url'),
            'app_name' => $app_name,
            'category_text' => $category_text,
        ], function ($message) use ($email, $title) {
            $message->to($email)->subject($title);
        });

        return Response::success([], '发送成功');
    }

    public function loginEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string'],
            'code' => [Rule::requiredIf(fn() => config('app.env') !== 'local'), 'integer'],
        ]);

        if ($validator->fails()) {
            return Response::error('邮箱格式不正确', 5001);
        }

        $email = strtolower(trim($request->email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'res_code' => 5003,
                'res_msg' => trans('app-return.email_format_error'),
                'data' => [],
            ]);
        }

        try {
            $payload = IdentityService::authenticate(UserIdentity::PROVIDER_EMAIL, $email);

            UserIdentity::query()
                ->where('provider', UserIdentity::PROVIDER_EMAIL)
                ->where('identifier', $email)
                ->update(['verified_at' => now()]);

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
