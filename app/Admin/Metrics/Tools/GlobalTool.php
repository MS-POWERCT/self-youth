<?php

namespace App\Admin\Metrics\Tools;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PragmaRX\Google2FA\Google2FA;

class GlobalTool
{

    public static function getImageMove()
    {
        return 'images/' . date('Ym') . '/day' . date('d');
    }
    public static function getImageMaxsize()
    {
        return 2048;
    }
    public static function getImageAccept()
    {
        return 'jpg,png,jpeg,webp';
    }


    public static $LANGS = [
        'zh_CN' => '中文',
        'en' => '英文',
        'ko' => '韩文',
        'ja' => '日文'
    ];

    public static $status = [
        0 => '关闭',
        1 => '开启',
    ];
    public static $status_color = [
        0 => 'danger',
        1 => 'success',
    ];

    public static $order_status_color = [
        'CREATED' => 'primary',
        'ENABLED' => 'success',
        'REDEEM' => 'info',
        'FINISHED' => 'danger',
        'CANCELLED' => 'warning',
    ];


    /**
     * 给字符串文字进行html的颜色,大小,粗细等
     * $str：需要设置颜色的字符串，类型为 string。
     * $color：设置字体颜色的参数，默认值为 'red'，类型为 string。
     * $font_size：设置字体大小的参数，默认值为 '16px'，类型为 string。
     * $weight：是否加粗字体的参数，默认值为 false，类型为 bool。
     */
    static function setColor(string $str, bool $weight = false, string $color = 'red', string $font_size = '14px'): string
    {
        $style = "color:{$color};font-size:{$font_size};";
        if ($weight) {
            $style .= 'font-weight:bold;';
        }
        return "<span style=\"{$style}\">{$str}</span>";
    }


    static function getBrowserUrl($value, $asset, $type)
    {
        $start = substr($value, 0, 8);
        $end = substr($value, -8);
        $url = $asset->browser_url . $type . '/';
        return "<a target='_blank' href='" . $url . "{$value}'>链上查看" . $start . "..." . $end . "</a>";
    }


    public static function getUser()
    {
        $user = Auth::guard('admin')->user();
        $user->admin_role_user = DB::table('admin_role_users')->where('user_id', $user->id)->first();
        $user->admin_role = DB::table('admin_roles')->where('id', $user->admin_role_user->role_id)->first();
        return $user;
    }

    // 验证google验证码
    public static function verifyGoogleCode($google2faCode, $user)
    {
        if ($google2faCode) {
            if (!(new Google2FA())->verifyKey($user->google_two_fa_secret, $google2faCode)) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }



    /**
     * 获取当前页码
     *
     * @return int
     */
    public static function getCurrentPage($class): int
    {
        return (int) request()->input('app-admin-renderable-' . strtolower(class_basename($class)) . '_page', 1);
    }
}
