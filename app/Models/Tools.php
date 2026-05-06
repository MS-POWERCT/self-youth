<?php

namespace App\Models;


class Tools
{

    // 机器颜色
    public static $miner_color = [
        0 => 'red',
        1 => 'yellow',
        2 => 'success',
    ];

    public static $status_color = [
        0 => '#17a2b8', // info
        1 => '#28a745', // success
        2 => '#dc3545', // danger
        3 => '#ffc107', // warning
        4 => '#007bff', // primary
        5 => '#6c757d', // secondary
        6 => '#353535', // light
        7 => '#343a40', // dark
        8 => '#000000', // black
        9 => '#ab7812', // orange
        10 => '#234567',
        11 => '#808080', // gray
    ];
    //
    public static $order_status_color = [
        'CREATED' => '#1677FF',
        'ENABLED' => '#52C41A',
        'REDEEM' => '#2F54EB',
        'PAUSED' => '#FAAD14',
        'FINISHED' => '#F5222D',
    ];



    /**
     * 访问器 - 修改字段在输出时的内容
     * @param  string  $value
     * @return string
     */
    public static function setPrefix($value, $action = '')
    {

        // 判断是否在 Dcat Admin 的表单调用中
        if (substr($action, -5) === '@edit') {
            return $value; // 不修改字段内容
        }
        if (substr($action, -7) === '@update') {
            return $value; // 不修改字段内容
        }
        if (substr($action, -8) === '@destroy') {
            return $value; // 不修改字段内容
        }

        if (empty($value)) {
            return $value;
        }

        // if (!@getimagesize($value)) {
        //     return env('OSS_CDN_DOMAIN_URL') . DIRECTORY_SEPARATOR . $value;
        // }

        if ($value) {
            if (env('APP_ENV') == 'local') {
                $server_ip = gethostbyname(gethostname());
                $request = request();
                return 'http://' . $server_ip . ':' . $request->getPort() . '/uploads/' . $value;
            } else {
                return env('APP_URL') . '/uploads/' . $value;
            }
        }
        return $value;
    }
}
