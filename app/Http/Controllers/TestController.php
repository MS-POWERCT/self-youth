<?php

namespace App\Http\Controllers;

use App\Api\MyController;
use App\Models\User;
use App\Services\FarmUserLandService;
use App\Services\FarmUserService;
use App\Services\FarmWarehouseService;
use App\Services\HabitService;
use App\Services\UserService;
use App\Support\Response;
use Illuminate\Http\Request;

class TestController
{

    public function test(Request $request)
    {
        // $total_exp = 0;
        // for ($i = 1; $i <= 50; $i++) {
        //     $exp = FarmUserService::getFarmUserNextLevelExp($i);
        //     $total_exp += $exp;
        // }
        // FarmUserService::printFarmUserNextLevelExp();


        $total = 0;
        for ($i = 10; $i <= 50; $i += 2) {
            // 已扩容次数（每次+2）
            $times = ($i - FarmWarehouseService::$FARM_DEFAULT_NUM) / FarmWarehouseService::$FARM_EXTEND_NUM;
            // 已达到最大容量（40个位置，扩容15次）
            if ($times >= FarmWarehouseService::$FARM_MAX_EXTEND_TIMES) {
                break;
            }
            // 第 $times 次扩容的价格（$times 从0开始）
            // 价格序列：300, 700, 1200, 1800, 2500, ...
            // 公式：300 + 400*times + 100*times*(times-1)/2
            $price = 200 + 400 * $times + 200 * $times * ($times - 1) / 2;
            $total += $price;
            dump("第{$times}次扩容价格：{$price}");
        }
        dump("总价格：{$total}");
    }
}
