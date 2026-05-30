<?php

namespace App\Api;

use App\Services\ToolsService;
use App\Support\Response;

class GlobalController extends Controller
{

    public function getInitData()
    {
        $global = [];

        // 将
        $llconfig = ToolsService::getLlconfigOption();

        $global['llconfig'] = $llconfig;

        return Response::success($global);
    }
}
