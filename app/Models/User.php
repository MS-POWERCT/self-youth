<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Redis;
use Laravel\Passport\HasApiTokens;
// use Spatie\Activitylog\LogOptions;
// use Spatie\Activitylog\Traits\LogsActivity;
use League\OAuth2\Server\Exception\OAuthServerException;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasDateTimeFormatter;

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->logOnly(['address', 'referrer_address']) // 只记录这些属性的变更
    //         ->logOnlyDirty() // 只记录属性值实际发生变更的情况
    //         ->dontSubmitEmptyLogs() // 如果没有属性发生变更，则不记录日志
    //         ->useLogName('system'); // 使用 'system' 作为日志名称
    // }

    public static $user_status_color = [
        0 => 'success',
        1 => 'red',
        2 => 'yellow',
        3 => 'yellow',
        4 => 'primary',
        9 => 'success',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];
    protected $guarded = [];
    public $incrementing = true; // 允许自增，同时也支持手动指定 ID

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($user) {
            if ($user->isDirty('name')) {
                $newName = $user->name;
                $userId = $user->id;
                Redis::hset("users_names", $userId, $newName);
            }
            if ($user->isDirty('avatar')) {
                $newAvatar = $user->avatar;
                $userId = $user->id;
                Redis::hset("users_avatars", $userId, $newAvatar);
            }
        });
    }

    public function getAvatarAttribute($value)
    {
        return Tools::setPrefix($value, request()->route()->getAction('controller'));
    }


    public function findForPassport($username)
    {

        // 先验证邮箱是否存在
        if (!User::where('email', $username)->first()) {
            throw new OAuthServerException(trans('app-return.email_not_register'), 99, 'invalid_grant');
        }

        return $this->where('email', $username)->where('status', '!=', 1)->first();
    }
}
