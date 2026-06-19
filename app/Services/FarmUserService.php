<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class FarmUserService
{

    // 每次种植得多少经验
    public static $FARM_PLANT_EXP = 2;
    // 每次铲除得多少经验
    public static $FARM_SHOVEL_EXP = 2;
    // 每次除草得多少经验
    public static $FARM_WATER_EXP = 3;
    // 每次杀虫得多少经验
    public static $FARM_KILL_EXP = 3;
    // 每次翻土的多少经验
    // public static $FARM_TILL_EXP = 3;


    // 用户升级条件 code... 条件是什么待定
    public static $FARM_UPGRADE_CONDITION = [
        9 => 1,
        19 => 1,
        29 => 1,
        39 => 1,
        49 => 1,
    ];

    // 给我一个等级返回用户下一级需要的经验
    public static function getFarmUserNextLevelExp(int $level)
    {
        $exp = 0;
        // 遍历升级条件，找到当前等级的升级条件
        for ($i = 0; $i < $level; $i++) {
            if ($i < 9) {
                $exp += 50;
            } else if ($i == 9) {
                $exp *= 2;
            } else if ($i < 19) {
                $exp += 100;
            } else if ($i == 19) {
                $exp *= 2;
            } else if ($i < 29) {
                $exp += 300;
            } else if ($i == 29) {
                $exp *= 3;
            } else if ($i < 39) {
                $exp += 800;
            } else if ($i == 39) {
                $exp *= 3;
            } else if ($i < 49) {
                $exp += 1500;
            } else if ($i == 49) {
                $exp *= 4;
            }
        }
        return $exp;
    }

    // 获取农场用户等级
    public static function getFarmUserLevel(int $user_id)
    {
        // 检查一下是否存在
        $exists = Redis::hexists('users_farm_level', $user_id);
        if (!$exists) {
            Redis::hset('users_farm_level', $user_id, 0);
        }
        return Redis::hget('users_farm_level', $user_id);
    }
    // 设置农场用户等级
    public static function setFarmUserLevel(int $user_id, int $level)
    {
        Redis::hset('users_farm_level', $user_id, $level);
    }

    // 经验添加
    public static function farmAddExp(int $user_id, int $experience)
    {
        Redis::hincrby('users_farm_exp', $user_id, $experience);
        // 获取用户经验
        $farm_user_exp = self::getFarmUserExp($user_id);
        // 检查是否触发用户升级
        $farm_user_level = self::getFarmUserLevel($user_id);

        // 检查用户经验是否满了
        $next_level_exp = self::getFarmUserNextLevelExp($farm_user_level + 1);
        if ($farm_user_exp >= $next_level_exp) {

            //如果用户可以升级了要判断是否是自动升级还是手动升级
            $upgrade_condition = self::$FARM_UPGRADE_CONDITION[$farm_user_level] ?? 0;
            if ($upgrade_condition) {
                // 这个就要用户自己去点击提示升级
            } else {
                // 自动升级
                self::setFarmUserLevel($user_id, $farm_user_level + 1);
                // 扣除经验
                Redis::hincrby('users_farm_exp', $user_id, -$next_level_exp);
            }
        }
    }
    // 获取用户经验
    public static function getFarmUserExp(int $user_id)
    {
        // 检查一下是否存在
        $exists = Redis::hexists('users_farm_exp', $user_id);
        if (!$exists) {
            Redis::hset('users_farm_exp', $user_id, 0);
        }
        return +Redis::hget('users_farm_exp', $user_id);
    }

    // 打印每个等级需要的经验
    public static function printFarmUserNextLevelExp()
    {
        $total_exp = 0;
        for ($i = 1; $i <= 50; $i++) {
            $exp = self::getFarmUserNextLevelExp($i);
            $total_exp += $exp;
            dump("等级{$i}需要{$exp}经验,当前总经验为{$total_exp}");
        }
    }
}
