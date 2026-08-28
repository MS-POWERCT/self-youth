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
        2 => 'red',
    ];

    protected $appends = ['quality_type_name'];

    public function getQualityTypeNameAttribute()
    {
        $types = trans('app-status.task.quality_type');
        if (isset($types[$this->quality_type])) {
            return $types[$this->quality_type];
        }
        return null;
    }

    // 奖励资产
    public function rewardAsset()
    {
        return $this->belongsTo(Asset::class, 'reward_asset_id');
    }

    //对应的npc
    public function npc()
    {
        return $this->belongsTo(FarmTaskNpc::class, 'npc_id')->select('id', 'name', 'icon');
    }
}
