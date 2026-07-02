<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\FarmDeliveryRecord;
use App\Models\FarmUserLand;
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
    public static $FARM_TILL_EXP = 3;


    // 配送工具配置
    public static $FARM_DELIVERY_TOOL = [
        [
            'id' => 0,
            'name' => "电动车",
            'icon' => "noto-v1:motor-scooter",
            'price' => 1000,
            'level_id' => 1,
            'asset_id' => 1,
            'capacity' => 10,  // 装载量
            'delivery_time' => 60, // 配送时间（分钟）
        ],
        [
            'id' => 1,
            'name' => "面包车",
            'icon' => "noto-v1:minibus",
            'price' => 10000,
            'level_id' => 10,
            'asset_id' => 1,
            'capacity' => 20,  // 装载量
            'delivery_time' => 40, // 配送时间（分钟）
        ],
        [
            'id' => 2,
            'name' => "货车",
            'icon' => "noto-v1:delivery-truck",
            'price' => 100000,
            'level_id' => 20,
            'asset_id' => 1,
            'capacity' => 50,  // 装载量
            'delivery_time' => 20, // 配送时间（分钟）
        ],
        [
            'id' => 3,
            'name' => "直升机",
            'icon' => "noto-v1:helicopter",
            'price' => 1000000,
            'level_id' => 30,
            'asset_id' => 1,
            'capacity' => 100,  // 装载量
            'delivery_time' => 5, // 配送时间（分钟）
        ]
    ];



    // // 用户升级条件 code... 条件是什么待定
    // public static $FARM_UPGRADE_CONDITION = [
    //     9 => 1,
    //     19 => 1,
    //     29 => 1,
    //     39 => 1,
    //     49 => 1,
    // ];

    // 给我一个等级返回用户下一级需要的经验
    public static function getFarmUserNextLevelExp(int $level)
    {
        return $level * 200;
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
            // $upgrade_condition = self::$FARM_UPGRADE_CONDITION[$farm_user_level] ?? 0;
            // if ($upgrade_condition) {
            //     // 这个就要用户自己去点击提示升级
            // } else {
            // 自动升级
            self::setFarmUserLevel($user_id, $farm_user_level + 1);
            // 扣除经验
            Redis::hincrby('users_farm_exp', $user_id, -$next_level_exp);
            // }
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

    /**
     * 获取用户配送工具列表
     * @param  \App\Models\User $user
     */
    public static function getFarmUserDeliveryToolList($user)
    {
        $delivery_tools = self::$FARM_DELIVERY_TOOL;
        $assets = Asset::pluck('name', 'id')->toArray();

        // 获得用户的工具
        $user_delivery_tools = json_decode(Redis::hget('users_delivery_tool', $user->id), true) ?? [];

        $delivery_records_list = FarmDeliveryRecord::with(['handbook'])
            ->select(['id', 'tool_id', 'num', 'handbook_id', 'asset_id', 'start_at', 'end_at', 'amount', 'status'])
            ->where('user_id', $user->id)
            ->where('status', 0)
            ->get();

        // 合并配送工具和资产信息
        foreach ($delivery_tools as $key => $tool) {
            $delivery_tools[$key]['asset_name'] = $assets[$tool['asset_id']] ?? '';
            // 检查用户是否有这个工具
            $delivery_tools[$key]['is_have'] = in_array($tool['id'], $user_delivery_tools) ? 1 : 0;
            // 是否配送和配送信息

            $delivery_tools[$key]['is_delivery'] = $delivery_records_list->contains('tool_id', $tool['id']) ? 1 : 0;
            $delivery_tools[$key]['delivery_record'] = $delivery_records_list->where('tool_id', $tool['id'])->first() ?? [];
        }

        return $delivery_tools;
    }

    /**
     * 收获后更新土地状态（进入下一季或枯萎）
     */
    public static function updateLandAfterHarvest(FarmUserLand $farm_land)
    {
        $farm_land->residue_output = 0;
        $farm_land->total_output = 0;

        if ($farm_land->handbook->quarter > $farm_land->quarter) {
            $farm_land->status = 1;
            $farm_land->quarter += 1;
            $farm_land->plant_mature_at = date('Y-m-d H:i:s', time() + ($farm_land->handbook->mature_after_time ?? $farm_land->handbook->mature_time));
        } else {
            $farm_land->status = 3;
        }

        $farm_land->save();
    }
}
