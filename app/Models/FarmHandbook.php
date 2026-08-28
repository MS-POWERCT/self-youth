<?php

namespace App\Models;

use App\Services\ToolsService;
use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class FarmHandbook extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'farm_handbook';
    protected $guarded = [];

    // 增加字段
    protected $appends = ['selling_asset_name'];

    /** 农场图标目录（相对 public） */
    public const ICON_PATH = '/images/farm/icons/';

    /** 解析图鉴 icon 为可访问 URL */
    public static function resolveIconUrl(?string $icon): ?string
    {
        if (!$icon) {
            return null;
        }

        if (preg_match('#^https?://#', $icon)) {
            return $icon;
        }

        if (str_starts_with($icon, '/')) {
            return $icon;
        }

        return self::ICON_PATH . ltrim($icon, '/');
    }

    public function getIconUrlAttribute(): ?string
    {
        return self::resolveIconUrl($this->attributes['icon'] ?? null);
    }

    // 返回selling 名称
    public function getSellingAssetNameAttribute()
    {
        // 检查是否存在
        if (!$this->selling_asset_id) {
            return '';
        }
        return ToolsService::getAssetName($this->selling_asset_id);
    }


    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function sellingAsset()
    {
        return $this->belongsTo(Asset::class, 'selling_asset_id', 'id');
    }
}
