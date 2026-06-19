<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class FarmShop extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'farm_shop';
    protected $guarded = [];


    public function handbook()
    {
        return $this->belongsTo(FarmHandbook::class, 'handbook_id', 'id');
    }
}
