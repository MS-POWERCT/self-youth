<?php

namespace App\Models;

use App\Services\ToolsService;
use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class FarmHandbook extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'farm_handbook';
    protected $guarded = [];

    // 增加字段
    protected $appends = ['selling_asset_name'];



    // 返回selling 名称
    public function getSellingAssetNameAttribute()
    {
        // 检查是否存在
        if (!$this->selling_asset_id) {
            return '';
        }
        return ToolsService::getAssetName($this->selling_asset_id);
    }


    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function sellingAsset()
    {
        return $this->belongsTo(Asset::class, 'selling_asset_id', 'id');
    }
}
