<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class FarmWarehouse extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'farm_warehouses';


    public function handbook()
    {
        return $this->belongsTo(FarmHandbook::class, 'handbook_id', 'id');
    }
}
