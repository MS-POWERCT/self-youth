<?php

namespace App\Services;

use App\Models\AppLog;

class AppLogService
{

    public static function addLog($morph_model, $morph_id, $log = 'system', $admin_user_id = 1)
    {
        return AppLog::create([
            'morph_model' => $morph_model,
            'morph_id' => $morph_id,
            'admin_user_id' => $admin_user_id,
            'log' => $log,
        ]);
    }
}
