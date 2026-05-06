<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class LoverCircle extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'lover_circles';
    protected $guarded = [];
    public static $status = [
        'CREATED' => '待审核',
        'ENABLED' => '正常',
        'PAUSED' => '暂停',
        'SENSITIVE' => '敏感',
        'DELETED' => '删除',
    ];
    public function user()
    {
        return $this->belongsTo('App\Models\User')->select('id', 'avatar', 'name');
    }

    public function comment()
    {
        return $this->hasMany(LoverComment::class, 'circle_id');
    }
}
