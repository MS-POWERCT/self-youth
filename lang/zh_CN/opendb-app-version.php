<?php 
return [
    'labels' => [
        'OpendbAppVersion' => 'OpendbAppVersion',
        'opendb-app-version' => 'OpendbAppVersion',
    ],
    'fields' => [
        'appid' => '应用的AppID',
        'name' => '应用名称',
        'title' => '更新标题',
        'contents' => '更新内容',
        'platform' => '更新平台，Android || iOS || [Android, iOS]',
        'type' => '安装包类型，native_app || wgt',
        'uni_platform' => 'uni平台信息，如：mp-weixin/web/app',
        'version' => '当前包版本号，必须大于当前线上发行版本号',
        'min_uni_version' => '原生App最低版本',
        'url' => '可下载安装包地址',
        'stable_publish' => '是否上线发行',
        'is_silently' => '是否静默更新',
        'is_mandatory' => '是否强制更新',
        'create_date' => '上传时间',
        'create_env' => '创建来源，uni-stat：uni统计自动创建，upgrade-center：升级中心管理员创建',
    ],
    'options' => [
    ],
];
