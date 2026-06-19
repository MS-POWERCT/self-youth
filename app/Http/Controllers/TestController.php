<?php

namespace App\Http\Controllers;

use App\Api\MyController;
use App\Models\User;
use App\Services\FarmUserService;
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
    }
}
