<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Laravel\Passport\HasApiTokens;
use League\OAuth2\Server\Exception\OAuthServerException;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasDateTimeFormatter;

    public static $user_status_color = [
        0 => 'success',
        1 => 'red',
        2 => 'yellow',
        3 => 'yellow',
        4 => 'primary',
        9 => 'success',
    ];

    protected $hidden = ['password'];

    protected $guarded = [];

    protected $appends = ['email', 'uuid', 'address', 'login_type'];

    public $incrementing = true;

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($user) {
            if ($user->isDirty('name')) {
                Redis::hset('users_names', $user->id, $user->name);
            }
            if ($user->isDirty('avatar')) {
                Redis::hset('users_avatars', $user->id, $user->avatar);
            }
        });
    }

    public function identities(): HasMany
    {
        return $this->hasMany(UserIdentity::class);
    }

    public function getAvatarAttribute($value)
    {
        return Tools::setPrefix($value, request()->route()?->getAction('controller'));
    }

    public function getEmailAttribute(): ?string
    {
        return $this->resolveIdentity(UserIdentity::PROVIDER_EMAIL)?->identifier;
    }

    public function getUuidAttribute(): ?string
    {
        return $this->resolveIdentity(UserIdentity::PROVIDER_VISITOR)?->identifier;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->resolveIdentity(UserIdentity::PROVIDER_WEB3)?->identifier;
    }

    public function getPasswordAttribute(): ?string
    {
        return $this->resolveIdentity(UserIdentity::PROVIDER_EMAIL)?->credential;
    }

    public function getLoginTypeAttribute(): ?string
    {
        $first = $this->relationLoaded('identities')
            ? $this->identities->sortBy('created_at')->first()
            : $this->identities()->orderBy('created_at')->first();

        if (!$first) {
            return null;
        }

        return match ($first->provider) {
            UserIdentity::PROVIDER_VISITOR => 'uuid',
            UserIdentity::PROVIDER_WEB3 => 'address',
            default => $first->provider,
        };
    }

    protected function resolveIdentity(string $provider): ?UserIdentity
    {
        if (!$this->relationLoaded('identities')) {
            $this->load('identities');
        }

        return $this->identities
            ->first(fn (UserIdentity $identity) => $identity->provider === $provider && $identity->status === UserIdentity::STATUS_ACTIVE);
    }

    public function findForPassport($username)
    {
        $identity = UserIdentity::query()
            ->where('provider', UserIdentity::PROVIDER_EMAIL)
            ->where('identifier', strtolower(trim($username)))
            ->where('status', UserIdentity::STATUS_ACTIVE)
            ->first();

        if (!$identity) {
            throw new OAuthServerException(trans('app-return.email_not_register'), 99, 'invalid_grant');
        }

        $user = $identity->user;

        if (!$user || $user->status == 1) {
            return null;
        }

        return $user;
    }

    public function validateForPassportPasswordGrant($password)
    {
        $credential = $this->resolveIdentity(UserIdentity::PROVIDER_EMAIL)?->credential;

        if (!$credential) {
            return false;
        }

        return Hash::check($password, $credential);
    }
}
