<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class FarmDeliveryRecord extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'farm_delivery_records';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $guarded = [];

    // 图谱关联
    public function handbook()
    {
        return $this->hasOne(FarmHandbook::class, 'id', 'handbook_id')->select(['id', 'name', 'selling_price', 'selling_asset_id']);
    }
}
