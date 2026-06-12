<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserLog;
use Illuminate\Support\Facades\Redis;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of My
 *
 * @author Administrator
 */
class UserService
{


    public static function getEmailCodeKey($email, $category)
    {
        return "email_code_{$category}:{$email}";
    }
    public static function getEmailCodeLimitKey($email, $category)
    {
        return "email_code_limit_{$category}:{$email}";
    }
    // 验证邮箱验证码
    public static function checkEmailCode($email, $category, $code)
    {

        // 格式检查
        if (!$email || !$code) return false;
        $key = self::getEmailCodeKey($email, $category);

        $cachedCode = Redis::get($key);
        // 验证码不存在或不匹配
        if (!$cachedCode || $cachedCode != $code) {
            return false;
        }
        // 一次性有效，验证完立即删除
        Redis::del($key);
        return true;
    }


    /**
     * 生成推荐码
     * 默认推荐码长度6
     * 如果用户量增多后5次撞表就加1位
     */
    // public static function setUserParentCode()
    // {
    //     $my_referral_code = '';
    //     $length = 6;
    //     $count = 0;
    //     do {
    //         $my_referral_code = ToolsService::getRandomStr($length, 1);
    //         $count += 1;
    //         $parent = User::where('referral_code', $my_referral_code)->select('id')->first();
    //         if (!empty($parent)) {
    //             if ($count % 5 == 0) {
    //                 $length += 1;
    //             }
    //             continue;
    //         }
    //         return $my_referral_code;
    //     } while (true);
    // }




    /**
     * 创建用户函数
     * @return type
     */
    public static function createUser($value, $type)
    {
        $preData = [
            $type => $value,
            'ip' => $GLOBALS['clientIp'],
            'status' => 0,
            'login_type' => $type,
            'name' => CreativeNameService::generateDe(),
        ];

        $user = User::create($preData);

        // 创建默认习惯
        HabitService::getDefaultHabit($user);


        return $user;
    }


    // 用户操作日志功能
    public static function addLog(Int $user_id, string $log, string $type = 'default', Int $morph_id = 0)
    {
        if ($type == 'mark') {
            $log = UserLog::where('user_id', $user_id)->where('morph_id', $morph_id)->first();
            if ($log) {
                $log->num += 1;
                $log->updated_at = now();
                $log->save();
            } else {
                UserLog::create([
                    'user_id' => $user_id,
                    'type' => $type,
                    'morph_id' => $morph_id,
                    'log' => $log,
                    'num' => 1
                ]);
            }
        } else {
            UserLog::create(compact('user_id', 'type', 'morph_id', 'log', 'morph_id'));
        }
    }
}
