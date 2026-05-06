<?php

namespace App\Services;

use App\Models\Tools;
use App\Models\User;
use Illuminate\Support\Facades\Redis;

class LoverCircleService
{


    /**
     * 整理部落圈列表数据
     */
    public static function organizeData($list, $user)
    {

        foreach ($list as $key => &$item) {

            $images = $item->images ? explode(',', $item->images) : [];
            foreach ($images as &$img) {
                $img = Tools::setPrefix($img);
            }
            $item->images = $images;

            if ($item->comment) {
                foreach ($item->comment as &$comment) {
                    $comment->name = Redis::hget('users_names', $comment->user_id);
                }
            }


            // 配置用户点赞信息
            $cache_key = 'lover_circle:like_' . $item->id;
            $likes = Redis::hgetall($cache_key);
            if (!empty($likes)) {
                $names = Redis::hmget('users_names', array_keys($likes));
                $item->likes = array_map(function ($user_id, $name) {
                    return ['id' => $user_id, 'name' => $name];
                }, array_keys($likes), $names);
            } else {
                $item->likes = [];
            }

            $item->is_like = +Redis::hexists($cache_key, $user->id); // 自己是否点赞
            $item->like_num = Redis::hlen($cache_key); // 点赞数量
        }

        return $list;
    }
}
