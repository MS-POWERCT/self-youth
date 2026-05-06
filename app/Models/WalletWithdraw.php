<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;

class WalletWithdraw extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'wallet_withdraws';
    protected $guarded = [];

    protected $appends = ['status_name', 'status_color'];
    public function getStatusNameAttribute()
    {
        $types = trans('app-status.wallet_withdraw.status');
        if (isset($types[$this->status])) {
            return $types[$this->status];
        }
        return null;
    }

    public function getStatusColorAttribute()
    {
        $types = self::$status_color;
        if (isset($types[$this->status])) {
            return $types[$this->status];
        }
        return null;
    }

    public static $status_color = [
        'CREATED' => '#ff6600',
        'AUDITED' => '#DDDD22',
        'SENT' => '#226DDD',
        'SUCCEEDED' => '#15A479',
        'FAILED' => '#ff3366',
        'CANCELED' => '#B3B3B3'
    ];

    // 用户
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    // 资产
    public function asset()
    {
        return $this->belongsTo('App\Models\Asset');
    }
}
