<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\HabitService;
use App\Support\Response;
use Illuminate\Http\Request;

class TestController
{

    public function test(Request $request)
    {

        $time = new \DateTime('2026-06-07');

        return $time->diff(now())->days;

        // TODO: test code here
        // $list = [
        //     'a' => 1,
        //     'b' => 2,
        // ];

        // $user = User::find(600001);
        // HabitService::getDefaultHabit($user);


        return Response::success($list);
    }
}
