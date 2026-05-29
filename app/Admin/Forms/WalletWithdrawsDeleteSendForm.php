<?php

namespace App\Admin\Forms;

use App\Admin\Metrics\Tools\GlobalTool;
use App\Models\WalletWithdraw;
use App\Services3rd\BscscanService;
use App\Services\AppLogService;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Dcat\Admin\Contracts\LazyRenderable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class WalletWithdrawsDeleteSendForm extends Form implements LazyRenderable
{
    use LazyWidget; // 使用异步加载功能

}
