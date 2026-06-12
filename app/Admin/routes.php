<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('announce', 'AnnounceController');
    $router->resource('advertise', 'AdvertiseController');

    // 用户信息
    $router->resource('user', 'UserController');
    $router->resource('userLog', 'UserLogController');



    // 标记模块
    $router->resource('markCategory', 'MarkCategoryController');
    $router->resource('markModule', 'MarkModuleController');
    $router->resource('markItem', 'MarkItemController');
    $router->resource('markUser', 'MarkUserController');

    // 习惯模块
    $router->resource('habitCheckLog', 'HabitCheckLogController');
    $router->resource('habitValueLog', 'HabitValueLogController');
    $router->resource('userHabitConfig', 'UserHabitConfigController');
    $router->resource('userHabit', 'UserHabitController');
    $router->resource('userHabitIcon', 'UserHabitIconController');


    // 资产
    $router->resource('walletAsset', 'WalletAssetController');
    $router->resource('walletAssetChange', 'WalletAssetChangeController');
    $router->resource('walletWithdraws', 'WalletWithdrawController');
    $router->resource('walletDeposit', 'WalletDepositController');


    // 其他
    $router->resource('llconfig', 'LlconfigController');
    $router->resource('asset', 'AssetController');
});
