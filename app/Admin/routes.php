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

    // 标记模块
    $router->resource('markCategory', 'MarkCategoryController');
    $router->resource('markModule', 'MarkModuleController');
    $router->resource('markItem', 'MarkItemController');
    $router->resource('markUser', 'MarkUserController');

    // 资产
    $router->resource('walletAsset', 'WalletAssetController');
    $router->resource('walletAssetChange', 'WalletAssetChangeController');
    $router->resource('walletWithdraws', 'WalletWithdrawController');
    $router->resource('walletDeposit', 'WalletDepositController');


    // 其他
    $router->resource('llconfig', 'LlconfigController');
    $router->resource('asset', 'AssetController');
});
