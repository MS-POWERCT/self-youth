<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class FarmTask extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'farm_tasks';
    protected $guarded = [];

    protected $casts = [
        'task_need' => 'json',
    ];

    public static $quality_type_color = [
        0 => 'primary',
        1 => 'success',
        2 => 'info',
    ];

    // 奖励资产
    public function rewardAsset()
    {
        return $this->belongsTo(Asset::class, 'reward_asset_id');
    }
}
