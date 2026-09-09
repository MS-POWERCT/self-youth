<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FarmAsset extends Model
{
    use HasDateTimeFormatter, LogsActivity;

    protected $table = 'farm_assets';

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['balance'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('system');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function changes()
    {
        return $this->hasMany(FarmAssetChange::class, 'farm_asset_id');
    }
}
