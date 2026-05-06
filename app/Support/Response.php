<?php

namespace App\Support;

class Response
{


    /**
     * 返回成功的JSON响应
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return Response
     */
    public static function success($data = [], string $message = '', int $code = 0)
    {
        // 如果未传入消息，则使用默认成功提示
        $message = $message ?: trans('app-return.success');

        return response()->json(array('res_code' => $code, 'res_msg' => $message, 'data' => $data));
    }

    /**
     * 返回错误的JSON响应
     *
     * @param string $message
     * @param int $code
     * @param mixed $data
     * @return Response
     */
    public static function error(string $message = '', int $code = 9999, int $httpStatus = 200)
    {
        $message = $message ?: trans('app-return.error');
        return response()->json(array('res_code' => $code, 'res_msg' => $message, 'data' => ''), $httpStatus);
    }
}
