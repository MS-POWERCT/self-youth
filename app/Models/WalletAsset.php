<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WalletAsset extends Model
{
    use HasDateTimeFormatter, LogsActivity;
    protected $table = 'wallet_assets';
    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['balance', 'freeze', 'pledge'])->logOnlyDirty() // 只记录属性值实际发生变更的情况
            ->dontSubmitEmptyLogs() // 如果没有属性发生变更，则不记录日志
            ->useLogName('system'); // 使用 'system' 作为日志名称
    }

    // 用户
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


    public function asset()
    {
        return $this->belongsTo('App\Models\Asset');
    }
}
