<?php

return [

    'client_id' => env('TAPTAP_CLIENT_ID'),

    // 控制台 Server Secret，部分服务端接口可能需要；MAC 登录验签使用 SDK 下发的 mac_key
    'client_token' => env('TAPTAP_CLIENT_TOKEN'),

    // 国内：https://open.tapapis.cn  海外：https://open.tapapis.com
    'api_host' => env('TAPTAP_API_HOST', 'https://open.tapapis.cn'),

    // 优先拉详细信息（需 public_profile）；失败时会回退 basic-info
    'profile_path' => env('TAPTAP_PROFILE_PATH', '/account/profile/v1'),

    'basic_info_path' => env('TAPTAP_BASIC_INFO_PATH', '/account/basic-info/v1'),

];
