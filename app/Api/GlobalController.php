<?php

namespace App\Api;

use App\Support\Response;

class GlobalController extends Controller
{

    public function getInitData()
    {
        return Response::success();
    }
}
