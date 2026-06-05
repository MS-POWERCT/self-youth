<?php

namespace App\Http\Middleware;

use App\Services\ToolsService;
use App\Support\Response;
use Closure;
use Illuminate\Support\Facades\App;

class GlobalData
{
    public function handle($request, Closure $next)
    {

        $ip = $request->ip() ?? '127.0.0.1';
        $GLOBALS['clientIp'] = $ip;
        // ToolsService::getRealIp($ip);

        // 版本
        $version = $request->header('version') ?? '1.1.1';
        $GLOBALS['version'] = $version;

        // 只能是zh_CN,en,ko,ja
        $lang = $request->header('lang') ?? 'zh_CN';
        if (!in_array($lang, ['zh_CN', 'en', 'ko', 'ja'])) {
            return Response::error('lang error', 5000);
        }
        $GLOBALS['user_lang'] = $lang;
        App::setLocale($lang);


        // 如果参数有address 都修改为小写并保存,不是后台接口才执行
        if (!str_contains($request->url(), 'admin')) {
            if ($request->has('address') && $request->address) {
                $address = strtolower(trim($request->address));
                if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $address)) {
                    return Response::error(trans('app-return.address_format_error'), 5000);
                }
                $request->merge(['address' => $address]);
            }
        }

        // 获取机器码
        $GLOBALS['user_imei'] = $request->header('imei') ?? '0xxx1234567890';

        return $next($request);
    }
}
