<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;

class Advertise extends Model
{
    use HasDateTimeFormatter;

    public static $positions = [
        'HOME' => '首页',
        'DAPP' => 'dapp页面'
    ];
    /**
     * 访问器 - 修改字段在输出时的内容
     *
     * @param  string  $value
     * @return string
     */
    public function getImgUrlAttribute($value)
    {
        return Tools::setPrefix($value, request()->route()->getAction('controller'));
    }
}
