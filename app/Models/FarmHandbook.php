<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class FarmHandbook extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'farm_handbook';
    protected $guarded = [];


    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function sellingAsset()
    {
        return $this->belongsTo(Asset::class, 'selling_asset_id', 'id');
    }
}
