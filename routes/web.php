<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return 1;
});

Route::get('/getTest', function () {
    return 1;
});

Route::get('/test/test', "App\Http\Controllers\TestController@test");



// Cronjobs 其他接口
// Route::middleware('limit_api_repeat:8')->group(function () {});

// 检查oss是否注册
// Route::get('/check-providers', function () {
//     // 如果oss 未生效 运行自动加载
//     // composer dump-autoload
//     // php artisan package:discover
//     $providers = app()->getLoadedProviders();
//     return in_array('Iidestiny\LaravelFilesystemOss\OssStorageServiceProvider', array_keys($providers))
//         ? 'OSS 提供者已注册'
//         : 'OSS 提供者未注册';
// });
