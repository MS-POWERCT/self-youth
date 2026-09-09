<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailLoginService
{
    public static function checkLoginCode(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string'],
            'code' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first(), 5001);
        }

        $email = strtolower(trim($request->email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception(trans('app-return.email_format_error'), 5003);
        }

        if (!UserService::checkEmailCode($email, 'login', $request->code)) {
            throw new Exception('验证码错误或已过期', 5001);
        }
    }
}
