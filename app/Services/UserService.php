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

    // 默认常量
    public static $HANDBOOK_ID_DEFAULT = 1; // 默认手册id
    public static $HANDBOOK_NUM_DEFAULT = 6; // 默认种子数量
    public static $WALLET_ASSET_ID_DEFAULT = 1; // 默认资产
    public static $WALLET_ASSET_NUM_DEFAULT = 100; // 默认资产数量


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

        // 创建用户默认资产
        $wallet_asset = WalletAssetService::getWalletAsset($user, self::$WALLET_ASSET_ID_DEFAULT);
        // 默认给100
        WalletAssetService::change($wallet_asset, self::$WALLET_ASSET_NUM_DEFAULT, [
            'module_code' => 'ADMIN',
        ]);
        // 给这个用户仓库增加6个土豆
        $warehouse = FarmWarehouseService::getUserWareHouse($user, self::$HANDBOOK_ID_DEFAULT, 'seed');
        $warehouse->num += self::$HANDBOOK_NUM_DEFAULT;
        $warehouse->save();

        return $user;
    }


    // 用户操作日志功能
    public static function addLog(Int $user_id, string $log, string $type = 'default')
    {
        return UserLog::create(compact('user_id', 'type', 'log'));
    }
}
