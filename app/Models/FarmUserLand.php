<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class FarmUserLand extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'farm_user_lands';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function handbook()
    {
        return $this->belongsTo(FarmHandbook::class, 'handbook_id', 'id');
    }
}
