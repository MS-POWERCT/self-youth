<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class MarkItem extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'mark_item';

    protected $guarded = [];

    public function module()
    {
        return $this->belongsTo(MarkModule::class, 'module_id');
    }

    public function getImgUrlAttribute($value)
    {
        return Tools::setPrefix($value, request()->route()->getAction('controller'));
    }
}
