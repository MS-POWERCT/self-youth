<?php

namespace App\Api\Auth;

use App\Api\Controller;
use App\Support\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisitorLoginController extends Controller
{
    public function loginVisitor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return Response::error('格式不正确', 5001);
        }

        return Response::error('目前已关闭该方法登录，请使用邮箱或其他方式登录', 6201);
    }
}
