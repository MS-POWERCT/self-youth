<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// 全局模块
Route::get('/global/getInitData', "App\Api\GlobalController@getInitData"); // 获取初始化数据

// Route::get('/advertise/getList', "App\Api\AdvertiseController@getList"); // banner列表
// Route::post('/announce/getList', "App\Api\AnnounceController@getList"); // 公告列表
// Route::post('/announce/getDetail', "App\Api\AnnounceController@getDetail"); // 公告详情
// Route::post('/announce/getPopup', "App\Api\AnnounceController@getPopup"); // 弹出公告




Route::middleware('auth:api')->group(function () {

    // 用户信息模块
    Route::post('/my/getMyInfo', "App\Api\MyController@getMyInfo");
    Route::post('/my/changePassword', "App\Api\MyController@changePassword"); // 重置密码


    // 用户习惯模块
    Route::post('/habit/getList', "App\Api\HabitController@getList"); // 获取习惯列表
    Route::post('/habit/getEditableList', "App\Api\HabitController@getEditableList"); // 获取可以编辑的习惯列表
    Route::post('/habit/create', "App\Api\HabitController@create"); // 新增习惯
    Route::post('/habit/edit', "App\Api\HabitController@edit"); // 编辑习惯
    Route::post('/habit/hide', "App\Api\HabitController@hide"); // 隐藏/显示
    Route::post('/habit/delete', "App\Api\HabitController@delete"); // 删除习惯
    Route::get('/habit/stat', "App\Api\HabitController@stat"); // 获取打卡统计（周/月）
    Route::get('/habit/getIconList', "App\Api\HabitController@getIconList"); // 获取icon列表


    // 习惯打卡模块
    Route::post('/habit/check/toggle', "App\Api\HabitCheckController@toggle"); // 今日打卡/取消打卡
    Route::get('/habit/check/today', "App\Api\HabitCheckController@today"); // 今日打卡记录

    // 时长计数记录模块
    Route::post('/habit/value/create', "App\Api\HabitValueController@create"); // 新增数值记录
    Route::get('/habit/value/list', "App\Api\HabitValueController@getList"); // 获取数值记录列表
    Route::post('/habit/value/edit', "App\Api\HabitValueController@edit"); // 编辑数值记录
    Route::post('/habit/value/del', "App\Api\HabitValueController@del"); // 删除数值记录


    // 标记吧
    Route::get('/mark/getCategoryList', "App\Api\MarkController@getCategoryList");
    Route::get('/mark/getModuleList', "App\Api\MarkController@getModuleList");
    Route::get('/mark/getItemList', "App\Api\MarkController@getItemList");
    Route::post('/mark/markItem', "App\Api\MarkController@markItem");
    Route::post('/mark/batchMarkItem', "App\Api\MarkController@batchMarkItem");


    // 情侣圈
    Route::post('/loverCircle/create', "App\Api\LoverCircleController@create");
    Route::post('/loverCircle/getList', "App\Api\LoverCircleController@getList");
    Route::post('/loverCircle/userClick', "App\Api\LoverCircleController@userClick");
    Route::post('/loverCircle/delData', "App\Api\LoverCircleController@delData");
    Route::post('/loverComment/create', "App\Api\LoverCommentController@create"); // 评论
    Route::post('/loverComment/getList', "App\Api\LoverCommentController@getList"); // 评论列表
    Route::post('/loverComment/delData', "App\Api\LoverCommentController@delData"); // 删除评论


});



// Route::middleware('web3.auth')->group(function () {
// 用户信息模块
// Route::post('/my/getMyInfo', "App\Api\MyController@getMyInfo");
// Route::post('/my/getWalletAsset', "App\Api\MyController@getWalletAsset");
// Route::post('/my/myTeam', "App\Api\MyController@myTeam");
// Route::post('/walletWithdraw/create', "App\Api\WalletWithdrawController@create")->middleware(['limit_form_repeat', 'web3.signature']);

// // 充值提现
// Route::post('/walletWithdraw/create', "App\Api\WalletWithdrawController@create")->middleware('limit_form_repeat');
// Route::post('/walletWithdraw/getList', "App\Api\WalletWithdrawController@getList");
// Route::post('/walletAccountAssetChange/getList', "App\Api\WalletAccountAssetChangeController@getList");
// Route::post('/walletTransfer/getList', "App\Api\WalletTransferController@getList"); // 划转列表
// Route::post('/walletTransfer/create', "App\Api\WalletTransferController@create")->middleware(['limit_form_repeat']); // 创建划转
// });


// 账户模块
// 目前还缺密码登录，忘记密码，绑定地址/绑定邮箱
Route::post('/auth/email/sendCode', "App\Api\Auth\EmailLoginController@sendEmailCode"); // 发送验证码
Route::post('/auth/email/loginEmail', "App\Api\Auth\EmailLoginController@loginEmail"); // 邮箱登录
Route::as('web3')->prefix('web3')->group(function () { // web3 签名登录
    Route::get('signature', "App\Api\Auth\Web3LoginController@signature")->middleware('limit_form_repeat:3');
    Route::post('login', "App\Api\Auth\Web3LoginController@login")->middleware(['limit_form_repeat:3', 'web3.signature']);
});
