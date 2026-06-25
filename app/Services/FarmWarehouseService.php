<?php

namespace App\Services;

use App\Models\FarmWarehouse;

class FarmWarehouseService
{

    public static $FARM_DEFAULT_NUM = 10; // 仓库默认数量
    public static $FARM_DEES_MAX = 100; // 仓库最大数量


    // 获取用户仓库
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
}
