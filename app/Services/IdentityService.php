<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserIdentity;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class IdentityService
{
    /**
     * 兼容历史 type 参数：email / address / web3
     */
    public static function normalizeProvider(string $provider): string
    {
        return match ($provider) {
            'address' => UserIdentity::PROVIDER_WEB3,
            default => $provider,
        };
    }

    public static function normalizeIdentifier(string $provider, string $identifier): string
    {
        $identifier = trim($identifier);

        return match (self::normalizeProvider($provider)) {
            UserIdentity::PROVIDER_EMAIL, UserIdentity::PROVIDER_WEB3 => strtolower($identifier),
            default => $identifier,
        };
    }

    public static function findIdentity(string $provider, string $identifier, bool $activeOnly = true): ?UserIdentity
    {
        $provider = self::normalizeProvider($provider);
        $identifier = self::normalizeIdentifier($provider, $identifier);

        $query = UserIdentity::query()
            ->where('provider', $provider)
            ->where('identifier', $identifier);

        if ($activeOnly) {
            $query->where('status', UserIdentity::STATUS_ACTIVE);
        }

        return $query->first();
    }

    public static function findUserByIdentity(string $provider, string $identifier): ?User
    {
        return self::findIdentity($provider, $identifier)?->user;
    }

    public static function identityExists(string $provider, string $identifier): bool
    {
        return self::findIdentity($provider, $identifier) !== null;
    }

    public static function createIdentity(
        User $user,
        string $provider,
        string $identifier,
        ?string $credential = null,
        ?array $metadata = null
    ): UserIdentity {
        $provider = self::normalizeProvider($provider);
        $identifier = self::normalizeIdentifier($provider, $identifier);

        return UserIdentity::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'identifier' => $identifier,
            'credential' => $credential,
            'metadata' => $metadata,
            'status' => UserIdentity::STATUS_ACTIVE,
        ]);
    }

    public static function bindIdentity(User $user, string $provider, string $identifier, ?string $credential = null): UserIdentity
    {
        $provider = self::normalizeProvider($provider);
        $identifier = self::normalizeIdentifier($provider, $identifier);

        if (self::identityExists($provider, $identifier)) {
            throw new Exception('identity_already_bound');
        }

        if (self::hasIdentity($user, $provider)) {
            throw new Exception('user_already_has_provider');
        }

        $identity = self::createIdentity($user, $provider, $identifier, $credential);
        $user->unsetRelation('identities');

        return $identity;
    }

    public static function hasIdentity(User $user, string $provider): bool
    {
        $provider = self::normalizeProvider($provider);

        return $user->identities()
            ->where('provider', $provider)
            ->where('status', UserIdentity::STATUS_ACTIVE)
            ->exists();
    }

    public static function getIdentity(User $user, string $provider): ?UserIdentity
    {
        $provider = self::normalizeProvider($provider);

        if (!$user->relationLoaded('identities')) {
            $user->load('identities');
        }

        return $user->identities
            ->first(fn (UserIdentity $identity) => $identity->provider === $provider && $identity->status === UserIdentity::STATUS_ACTIVE);
    }

    public static function getIdentifier(User $user, string $provider): ?string
    {
        return self::getIdentity($user, $provider)?->identifier;
    }

    public static function updateEmailPassword(User $user, string $password): void
    {
        $identity = self::getIdentity($user, UserIdentity::PROVIDER_EMAIL);

        if (!$identity) {
            throw new Exception('email_not_bound');
        }

        $identity->credential = Hash::make($password);
        $identity->save();
        $user->unsetRelation('identities');
    }

    public static function touchLogin(User $user, string $provider): void
    {
        self::getIdentity($user, $provider)?->update(['last_login_at' => now()]);
    }

    /**
     * @return array{res_code:int,res_msg:string,access_token:string}
     */
    public static function authenticate(string $provider, string $identifier): array
    {
        $provider = self::normalizeProvider($provider);
        $identifier = self::normalizeIdentifier($provider, $identifier);

        $user = self::findUserByIdentity($provider, $identifier);

        if (!$user) {
            $legacyType = $provider === UserIdentity::PROVIDER_WEB3 ? 'address' : $provider;
            $user = UserService::createUser($identifier, $legacyType);
        }

        if ($user->status == 1) {
            throw new Exception(trans('app-return.acount_not_exist'), 1235);
        }

        $user->tokens()->delete();
        $accessToken = $user->createToken('api')->accessToken;
        self::touchLogin($user, $provider);

        return [
            'res_code' => 0,
            'res_msg' => trans('app-return.welcome_msg'),
            'access_token' => $accessToken,
        ];
    }

    public static function applyUserFilter(Builder $query, string $provider, string $value): Builder
    {
        $provider = self::normalizeProvider($provider);
        $value = trim($value);

        if ($value === '') {
            return $query;
        }

        if ($provider === UserIdentity::PROVIDER_WEB3) {
            $value = strtolower($value);
        }

        return $query->whereHas('identities', function (Builder $identityQuery) use ($provider, $value) {
            $identityQuery
                ->where('provider', $provider)
                ->where('identifier', 'like', "%{$value}%");
        });
    }
}
