<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class WalletDeposit extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'wallet_deposits';
    protected $guarded = [];
    protected $appends = ['status_name', 'status_color'];
    public function getStatusNameAttribute()
    {
        $types = trans('app-status.wallet_deposit.status');
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
        'LACKED' => '#ff6600',
        'SUCCESS' => '#15A479',
        'FAILED' => '#ff3366',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
