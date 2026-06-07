<?php

namespace App\Api;

use App\Models\OpendbAppVersion;
use App\Services\I18nService;
use App\Support\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppUpdateController extends Controller
{

    /**
     * 版本更新版本
     */
    public function version(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'appid' => 'required|string|max:50',
            'platform' => 'required|string|max:50',
        ]);
        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        $appid = $request->appid;
        $platform = $request->platform;



        $app_version = OpendbAppVersion::where('appid', $appid)->where('platform', $platform)->where('stable_publish', 1)->get();

        $app_version = I18nService::getTranslateList($app_version, OpendbAppVersion::class);

        return Response::success($app_version);
    }
}
