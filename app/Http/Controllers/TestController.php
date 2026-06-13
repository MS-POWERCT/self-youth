<?php

namespace App\Http\Controllers;

use App\Api\MyController;
use App\Models\User;
use App\Services\HabitService;
use App\Services\UserService;
use App\Support\Response;
use Illuminate\Http\Request;

class TestController
{

    public function test(Request $request)
    {

        return view('emails.new_code', [
            'app_name' => config('app.name'),
            'category_text' => 'Login',
            'code' => 123456,
            'time' => 60,
            'url' => config('app.url')
        ]);
    }
}
