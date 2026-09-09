<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;

class FarmAssetChange extends Model
{
    use HasDateTimeFormatter;

    protected $table = 'farm_asset_changes';

    protected $guarded = [];

    protected $appends = ['module_code_name'];

    public function getModuleCodeNameAttribute()
    {
        $types = trans('app-status.farm_asset.module_code');

        return $types[$this->module_code] ?? null;
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function farmAsset()
    {
        return $this->belongsTo(FarmAsset::class, 'farm_asset_id');
    }
}
