<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Announce extends Model
{
    use HasDateTimeFormatter;
    protected $guarded = [];
    public $timestamps = false;

    public static $postion = [
        'HOME' => '首页(默认)',
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

    // 清理缓存
    public static function clearCache($postion)
    {
        $cache_key = 'announce:' . $postion . '*';
        $keys = Redis::keys($cache_key);
        if (!empty($keys)) {
            Redis::pipeline(function ($pipe) use ($keys) {
                foreach ($keys as $key) {
                    $pipe->del(substr($key, getRedisPrefixLen()));
                }
            });
        }
    }
}
