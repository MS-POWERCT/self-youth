<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class UserReferrerService
{

    public static function getCacheUsers()
    {
        return Redis::hgetall("users_referrers");
    }

    public static function getUserList($user_id)
    {

        // 查询数据库
        // $user = User::find($user_id);
        // if (empty($user)) {
        //     return [];
        // }
        // $user_list = [];
        // $referrer = User::select('id', 'referrer_id')->where('id', $user->referrer_id)->first();
        // while ($referrer && !in_array($referrer->id, $user_list)) {
        // $user_list[] = $referrer->id;
        // $referrer = User::select('id', 'referrer_id')->where('id', $referrer->referrer_id)->first();
        // }

        // 可以通过以下缓存方法提高性能,但注意维护缓存关系列表
        $user_list = [];
        $referrer = Redis::hget('users_referrers', $user_id) ?? null;
        while ($referrer && !in_array($referrer, $user_list)) {
            $user_list[] = $referrer;
            $referrer = Redis::hget('users_referrers', $referrer) ?? null;
        };
        return $user_list;
    }



    public static function updateCacheUsers($user_id, $referrer_id)
    {
        Redis::hset("users_referrers", $user_id, $referrer_id);
    }



    // 刷新用户表的关系到缓存中
    public static function setUserReferrer()
    {
        $users = User::select('id', 'referrer_id')->get();
        // 对缓存数据进行更新
        Redis::pipeline(function ($pipe) use ($users) {
            foreach ($users as $value) {
                $pipe->hset('users_referrers', $value->id, $value->referrer_id);
            }
        });
    }

    // 刷新用户名称的关系到缓存中
    public static function setUserName()
    {
        Log::info('setUserName');
        $users = User::select('id', 'name')->get();
        // 对缓存数据进行更新
        Redis::pipeline(function ($pipe) use ($users) {
            foreach ($users as $value) {
                $pipe->hset('users_names', $value->id, $value->name);
            }
        });
    }

    // 刷新用户头像的关系到缓存中
    public static function setUserAvatar()
    {
        $users = User::select('id', 'avatar')->get();
        // 对缓存数据进行更新
        Redis::pipeline(function ($pipe) use ($users) {
            foreach ($users as $value) {
                $pipe->hset('users_avatars', $value->id, $value->avatar);
            }
        });
    }
}
