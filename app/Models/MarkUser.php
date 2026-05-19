<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class MarkUser extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'mark_user';

    protected $guarded = [];

    public const MARK_TYPE_COLOR = [
        0 => 'info',
        1 => 'primary',
        2 => 'success',
    ];


    public function module()
    {
        return $this->hasOne(MarkModule::class, 'id', 'module_id');
    }

    public function item()
    {
        return $this->hasOne(MarkItem::class, 'id', 'item_id');
    }
}
