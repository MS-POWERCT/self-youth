<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class MarkModule extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'mark_module';

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(MarkCategory::class, 'category_id');
    }

    public function getImgUrlAttribute($value)
    {
        return Tools::setPrefix($value, request()->route()->getAction('controller'));
    }
}
