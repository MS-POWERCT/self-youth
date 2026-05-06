<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class WalletAssetChange extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'wallet_asset_changes';
    protected $guarded = [];

    protected $appends = ['module_code_name'];

    public function getModuleCodeNameAttribute()
    {
        $types = trans('app-status.wallet_asset.module_code');
        if (isset($types[$this->module_code])) {
            return $types[$this->module_code];
        }
        return null;
    }

    // 资产
    public function asset()
    {
        return $this->belongsTo('App\Models\Asset');
    }

    // 用户
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
