<?php

namespace App\Api;

use App\Models\User;
use App\Support\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MyController extends Controller
{
    // 用户个人数据
    public function getMyInfo()
    {
        $user = User::find(Auth::id());
        return Response::success($user);
    }



    // 设置密码/重置密码
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
            'code' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 1212);
        }

        $password = $request->input('password');
        $user = User::find(Auth::id());

        // 检查是否有绑定邮箱
        if (!$user->email) {
            return Response::error('邮箱未绑定', '5001');
        }

        // if (!UserService::checkEmailCode($user->email, 'recover', $request->code)) {
        //     return Response::error('验证码错误或已过期', '5002');
        // }

        $user->password = Hash::make($password);
        $user->save();

        return Response::success();
    }
}
