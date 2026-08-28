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

        // 返回金币和经验的icon
        $global['gold_icon'] = 'cryptocurrency-color:gold';
        $global['exp_icon'] = 'meteocons:star-fill';

        return Response::success($global);
    }
}
