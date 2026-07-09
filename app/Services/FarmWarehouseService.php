<?php

namespace App\Services;

use App\Models\FarmWarehouse;
use Illuminate\Support\Facades\Redis;

class FarmWarehouseService
{
    // 背包单作物最大数量
    public static $FARM_DEES_MAX = 999;
    // 仓库单作物最大数量
    public static $FARM_WAREHOUSE_MAX = 999999;
    // 仓库默认大小数量
    public static $FARM_DEFAULT_NUM = 10;
    // 每次增加到数量
    public static $FARM_EXTEND_NUM = 2;
    // 仓库最大扩容次数
    public static $FARM_MAX_EXTEND_TIMES = 15;

    /**
     * 获取用户仓库
     * @param User $user 用户对象
     * @param int $handbook_id 手册id
     * @param int $type 仓库类型
     * @return mixed
     */
    static public function getUserWareHouse($user, $handbook_id, $type)
    {
        $warehouse = FarmWarehouse::where('user_id', $user->id)
            ->where('handbook_id', $handbook_id)->where('type', $type)->first();

        if (!$warehouse) {
            $warehouse = FarmWarehouse::create([
                'user_id' => $user->id,
                'handbook_id' => $handbook_id,
                'type' => $type,
                'num' => 0,
            ]);
        }
        return $warehouse;
    }

    /**
     * 获取指定图鉴ID的仓库数量
     * @param User $user 用户对象
     * @param array $handbookIds 图鉴ID列表
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getWareHouseList($user, array $handbookIds = [], $type = 'fruit')
    {
        $query = FarmWarehouse::where('user_id', $user->id)->where('type', $type);

        // 如果传入了图鉴ID，则按条件筛选
        if (!empty($handbookIds)) {
            $query->whereIn('handbook_id', $handbookIds);
        }

        return $query->get();
    }


    /**
     * 获取用户仓库大小
     * @param int $user_id 用户ID
     * @return int 仓库大小
     */
    public static function getWareHouseSize($user_id)
    {
        // 没有就创建使用默认值到redis中
        // 检查一下是否存在
        $exists = Redis::hexists('users_warehouse_size', $user_id);
        if (!$exists) {
            Redis::hset('users_warehouse_size', $user_id, self::$FARM_DEFAULT_NUM);
        }
        return Redis::hget('users_warehouse_size', $user_id);
    }
    /**
     * 增加用户仓库大小
     * @param int $user_id 用户ID
     * @param int $size 仓库大小
     */
    public static function addWareHouseSize($user_id, $size)
    {
        Redis::hincrby('users_warehouse_size', $user_id, $size);
    }
    /**
     * 获取用户仓库使用情况
     * @param int $user_id 用户ID
     * @return int 仓库使用情况
     */
    public static function getWareHouseUse($user_id)
    {
        return (int)FarmWarehouse::where('user_id', $user_id)->where('type', 'fruit')->where('num', '>', 0)->count();
    }
    /**
     * 得到下一个扩充价格
     * @param int $user_id 用户ID
     * @return int 下一个扩充价格，如果已满则返回0
     */
    public static function getNextExtendPrice($user_id)
    {
        $warehouse_size = self::getWareHouseSize($user_id);
        // 已扩容次数（每次+2）
        $times = ($warehouse_size - self::$FARM_DEFAULT_NUM) / self::$FARM_EXTEND_NUM;
        // 已达到最大容量（40个位置，扩容15次）
        if ($times >= self::$FARM_MAX_EXTEND_TIMES) {
            return 0;
        }
        $price = 200 + 400 * $times + 200 * $times * ($times - 1) / self::$FARM_EXTEND_NUM;
        return (int) $price;
    }

    /**
     * 判断新的入仓是否满了
     * @param int $user_id 用户ID
     * @param int $handbook_id 手册id
     * @return bool 是否满了
     */
    public static function isFullHouse($user_id, $handbook_id)
    {
        $exists = FarmWarehouse::where('user_id', $user_id)
            ->where('handbook_id', $handbook_id)
            ->where('type', 'fruit')
            ->where('num', '>', 0)
            ->exists();

        if ($exists) {
            return false;
        }

        $use = self::getWareHouseUse($user_id);
        $warehouse_size = self::getWareHouseSize($user_id);

        return $use >= $warehouse_size;
    }
}
