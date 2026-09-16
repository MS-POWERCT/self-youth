<?php

namespace App\Services;

use App\Models\UserIdentity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TapTapLoginService
{
    /**
     * 校验客户端提交的 TapTap Access Token 字段，并拉取用户公开信息。
     *
     * @return array{unionid:?string,openid:string,name:?string,avatar:?string}
     */
    public static function verifyAndGetProfile(Request $request): array
    {
        self::validateTokenParams($request);

        if (config('app.env') === 'local') {
            return self::localMockProfile($request);
        }

        self::ensureConfigured();

        $kid = trim((string) $request->input('kid'));
        $macKey = trim((string) $request->input('mac_key'));

        $profile = self::fetchProfile($kid, $macKey);

        if (empty($profile['unionid']) && empty($profile['openid'])) {
            throw new Exception('TapTap profile missing user identifier', 6303);
        }

        return $profile;
    }

    public static function validateTokenParams(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'kid' => ['required', 'string'],
            'mac_key' => ['required', 'string'],
        ], [
            'kid.required' => 'TapTap kid is required.',
            'mac_key.required' => 'TapTap mac_key is required.',
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first(), 6301);
        }
    }

    public static function ensureConfigured(): void
    {
        if (!config('taptap.client_id')) {
            throw new Exception('TapTap client is not configured', 6304);
        }
    }

    /**
     * @return array{unionid:?string,openid:string,name:?string,avatar:?string}
     */
    public static function login(Request $request): array
    {
        $profile = self::verifyAndGetProfile($request);
        $identifier = $profile['unionid'] ?: $profile['openid'];

        $metadata = array_filter([
            'openid' => $profile['openid'] ?? null,
            'unionid' => $profile['unionid'] ?? null,
            'name' => $profile['name'] ?? null,
            'avatar' => $profile['avatar'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');

        return IdentityService::authenticate(
            UserIdentity::PROVIDER_TAPTAP,
            $identifier,
            $metadata
        );
    }

    /**
     * @return array{unionid:?string,openid:string,name:?string,avatar:?string}
     */
    private static function fetchProfile(string $kid, string $macKey): array
    {
        $profilePath = config('taptap.profile_path', '/account/profile/v1');
        $response = self::requestTapTap($profilePath, $kid, $macKey);

        if (self::isInsufficientScope($response)) {
            $basicPath = config('taptap.basic_info_path', '/account/basic-info/v1');
            $response = self::requestTapTap($basicPath, $kid, $macKey);
        }

        if (!$response->successful()) {
            $error = $response->json('error') ?: 'taptap_request_failed';
            throw new Exception('TapTap verification failed: ' . $error, 6302);
        }

        $data = $response->json();

        return [
            'unionid' => $data['unionid'] ?? null,
            'openid' => $data['openid'] ?? '',
            'name' => $data['name'] ?? null,
            'avatar' => $data['avatar'] ?? null,
        ];
    }

    private static function requestTapTap(string $path, string $kid, string $macKey)
    {
        $clientId = config('taptap.client_id');
        $apiHost = rtrim((string) config('taptap.api_host'), '/');
        $host = parse_url($apiHost, PHP_URL_HOST);
        $scheme = parse_url($apiHost, PHP_URL_SCHEME) ?: 'https';
        $port = $scheme === 'https' ? '443' : '80';
        $uri = $path . '?client_id=' . rawurlencode($clientId);
        $url = $apiHost . $uri;

        $timestamp = (string) time();
        $nonce = Str::random(8);
        $method = 'GET';
        $signingString = self::buildSigningString($timestamp, $nonce, $method, $uri, $host, $port);
        $mac = self::sign($signingString, $macKey);
        $authorization = self::buildAuthorization($kid, $timestamp, $nonce, $mac);

        return Http::timeout(10)
            ->withHeaders(['Authorization' => $authorization])
            ->get($url);
    }

    private static function isInsufficientScope($response): bool
    {
        if ($response->successful()) {
            return false;
        }

        return $response->json('error') === 'insufficient_scope';
    }

    private static function buildSigningString(
        string $timestamp,
        string $nonce,
        string $method,
        string $uri,
        string $host,
        string $port
    ): string {
        return "{$timestamp}\n{$nonce}\n{$method}\n{$uri}\n{$host}\n{$port}\n\n";
    }

    private static function sign(string $signingString, string $macKey): string
    {
        return base64_encode(hash_hmac('sha1', $signingString, $macKey, true));
    }

    private static function buildAuthorization(string $kid, string $timestamp, string $nonce, string $mac): string
    {
        return sprintf('MAC id="%s",ts="%s",nonce="%s",mac="%s"', $kid, $timestamp, $nonce, $mac);
    }

    /**
     * @return array{unionid:?string,openid:string,name:?string,avatar:?string}
     */
    private static function localMockProfile(Request $request): array
    {
        return [
            'unionid' => $request->input('unionid', 'local_taptap_unionid'),
            'openid' => $request->input('openid', 'local_taptap_openid'),
            'name' => $request->input('name', 'Local TapTap User'),
            'avatar' => $request->input('avatar'),
        ];
    }
}
