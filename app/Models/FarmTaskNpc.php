<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;

class FarmTaskNpc extends Model
{
    use HasDateTimeFormatter;

    protected $table = 'farm_task_npc';

    protected $guarded = [];

    /** NPC 图标目录（相对 public） */
    public const ICON_PATH = '/images/farm/icons/npc/';

    /** 解析 NPC icon 为可访问 URL */
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
}
