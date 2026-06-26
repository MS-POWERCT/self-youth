<?php

namespace App\Services;

use App\Models\FarmWarehouse;

class FarmWarehouseService
{

    public static $FARM_DEFAULT_NUM = 10; // 仓库默认数量
    public static $FARM_DEES_MAX = 100; // 仓库最大数量


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
}
