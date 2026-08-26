<?php

namespace App\Api;

use App\Models\MarkUser;
use App\Models\User;
use App\Models\UserLog;
use App\Services\HabitService;
use App\Services\UserService;
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

        // 对密码进行处理，如果有设置密码就标记为已设置密码
        $user->has_password = $user->password ? 1 : 0;

        // 有2个值，一个是用户连续几天进行习惯，
        $user->continuous_days_check = HabitService::getContinuousDays($user->id, HabitService::HABITCHECK);
        $user->continuous_days_value = HabitService::getContinuousDays($user->id, HabitService::HABITVALUE);

        // 用户标记了多少个习惯
        $user->mark_user_count = MarkUser::where('user_id', Auth::id())->whereIn('mark_type', [1, 2])->count();

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

        if (!UserService::checkEmailCode($user->email, 'recover', $request->code)) {
            return Response::error('验证码错误或已过期', '5002');
        }

        $user->password = Hash::make($password);
        $user->save();

        return Response::success();
    }

    // 绑定邮箱
    public function bindEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'code' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 1212);
        }

        $email = $request->email;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(array('res_code' => 5003, 'res_msg' => trans('app-return.email_format_error'), 'data' => []));
        }
        $user = User::find(Auth::id());

        // 检查是否有绑定邮箱
        if ($user->email) {
            return Response::error('邮箱存在无法进行绑定', '5001');
        }

        if (!UserService::checkEmailCode($email, 'bind_email', $request->code)) {
            return Response::error('验证码错误或已过期', '5002');
        }

        $user->email = $email;
        $user->save();

        return Response::success();
    }

    // 绑定web3地址
    public function bindAddress(Request $request)
    {
        $address = $request->address;
        $user = User::find(Auth::id());

        // 检查是否有绑定邮箱
        if ($user->address) {
            return Response::error('地址存在无法进行绑定', '5001');
        }

        // 检查这个地址是否用户已绑定
        if (User::where('address', $address)->first()) {
            return Response::error('地址已绑定，请更换其他地址', '5002');
        }

        $user->address = $address;
        $user->save();

        return Response::success();
    }


    // 日志记录
    public function getUserLog()
    {
        return Response::success(UserLog::where('user_id', Auth::id())
            ->where('status', 1)->orderByDesc('updated_at')
            ->orderByDesc('id')->limit(50)->get());
    }



    // 填写个人信息
    public function fillInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'age'    => 'required|integer|min:1|max:150',
            'gender' => 'required|integer|in:0,1,2',
            'height' => 'required|integer|min:50|max:250',
        ], [
            'age.min'    => '年龄不能小于1岁',
            'age.max'    => '年龄不能大于150岁',
            'gender.in'  => '性别只能是0未知、1男、2女',
            'height.min' => '身高不能小于50cm',
            'height.max' => '身高不能大于250cm',
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 1212);
        }

        $user = User::find(Auth::id());
        $user->age = $request->age;
        $user->gender = $request->gender;
        $user->height = $request->height;
        $user->save();

        return Response::success();
    }
}
