<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class LoverComment extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'lover_comments';
    protected $guarded = [];

    protected $hidden = [
        'deleted_at',
        'updated_at'
    ];


    public function user()
    {
        return $this->belongsTo('App\Models\User')->select('id', 'avatar', 'name');
    }
}
