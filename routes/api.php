<?php

use Illuminate\Support\Facades\Route;

Route::get('/global/getInitData', 'App\Api\GlobalController@getInitData');

Route::middleware('auth:api')->group(function () {
    Route::post('/my/getMyInfo', 'App\Api\MyController@getMyInfo');
    Route::post('/my/changePassword', 'App\Api\MyController@changePassword')->middleware(['limit_form_repeat:3']);
    Route::post('/my/bindEmail', 'App\Api\MyController@bindEmail')->middleware(['limit_form_repeat:3']);
    Route::post('/my/bindAddress', 'App\Api\MyController@bindAddress')->middleware(['limit_form_repeat:3', 'web3.signature']);
    Route::post('/my/bindTapTap', 'App\Api\MyController@bindTapTap')->middleware(['limit_form_repeat:3', 'taptap.token']);
    Route::get('/my/getUserLog', 'App\Api\MyController@getUserLog');
    Route::post('/my/fillInfo', 'App\Api\MyController@fillInfo')->middleware(['limit_form_repeat:3']);

    Route::post('/habit/getList', 'App\Api\HabitController@getList');
    Route::post('/habit/getEditableList', 'App\Api\HabitController@getEditableList');
    Route::post('/habit/create', 'App\Api\HabitController@create')->middleware(['limit_form_repeat:3']);
    Route::post('/habit/edit', 'App\Api\HabitController@edit')->middleware(['limit_form_repeat:3']);
    Route::post('/habit/hide', 'App\Api\HabitController@hide');
    Route::post('/habit/delete', 'App\Api\HabitController@delete');
    Route::get('/habit/stat', 'App\Api\HabitController@stat');
    Route::get('/habit/getIconList', 'App\Api\HabitController@getIconList');

    Route::post('/habit/check/toggle', 'App\Api\HabitCheckController@toggle');
    Route::get('/habit/check/today', 'App\Api\HabitCheckController@today');
    Route::post('/habit/value/create', 'App\Api\HabitValueController@create')->middleware(['limit_form_repeat:3']);
    Route::get('/habit/value/list', 'App\Api\HabitValueController@getList');
    Route::post('/habit/value/edit', 'App\Api\HabitValueController@edit')->middleware(['limit_form_repeat:3']);
    Route::post('/habit/value/del', 'App\Api\HabitValueController@del')->middleware(['limit_form_repeat:3']);

    Route::get('/mark/getCategoryList', 'App\Api\MarkController@getCategoryList');
    Route::get('/mark/getModuleList', 'App\Api\MarkController@getModuleList');
    Route::get('/mark/getItemList', 'App\Api\MarkController@getItemList');
    Route::post('/mark/markItem', 'App\Api\MarkController@markItem');
    Route::post('/mark/batchMarkItem', 'App\Api\MarkController@batchMarkItem');

    Route::post('/loverCircle/create', 'App\Api\LoverCircleController@create')->middleware(['limit_form_repeat:3']);
    Route::post('/loverCircle/getList', 'App\Api\LoverCircleController@getList');
    Route::post('/loverCircle/userClick', 'App\Api\LoverCircleController@userClick');
    Route::post('/loverCircle/delData', 'App\Api\LoverCircleController@delData');
    Route::post('/loverComment/create', 'App\Api\LoverCommentController@create')->middleware(['limit_form_repeat:3']);
    Route::post('/loverComment/getList', 'App\Api\LoverCommentController@getList');
    Route::post('/loverComment/delData', 'App\Api\LoverCommentController@delData')->middleware(['limit_form_repeat:3']);

    Route::post('/weightRecord/create', 'App\Api\WeightRecordController@create')->middleware(['limit_form_repeat:3']);
    Route::post('/weightRecord/getList', 'App\Api\WeightRecordController@getList');
    Route::post('/weightRecord/getDetail', 'App\Api\WeightRecordController@getDetail');
    Route::post('/weightRecord/edit', 'App\Api\WeightRecordController@edit');
    Route::post('/weightRecord/del', 'App\Api\WeightRecordController@del');
    Route::get('/weightRecord/stats', 'App\Api\WeightRecordController@stats');
    Route::get('/weightRecord/chart', 'App\Api\WeightRecordController@chart');

    Route::post('/farmUser/initFarm', 'App\Api\FarmUserController@initFarm');
    Route::post('/farmUser/getLandList', 'App\Api\FarmUserController@getLandList');
    Route::post('/farmUser/plant', 'App\Api\FarmUserController@plant');
    Route::post('/farmUser/plantAll', 'App\Api\FarmUserController@plantAll');
    Route::post('/farmUser/remove', 'App\Api\FarmUserController@remove');
    Route::post('/farmUser/removeAll', 'App\Api\FarmUserController@removeAll');
    Route::post('/farmUser/refresh', 'App\Api\FarmUserController@refresh');
    Route::post('/farmUser/harvest', 'App\Api\FarmUserController@harvest');
    Route::post('/farmUser/harvestAll', 'App\Api\FarmUserController@harvestAll');
    Route::post('/farmUser/getLandUpgradeInfo', 'App\Api\FarmUserController@getLandUpgradeInfo');
    Route::post('/farmUser/upgradeLand', 'App\Api\FarmUserController@upgradeLand');
    Route::post('/farmUser/getSpecialInfo', 'App\Api\FarmUserController@getSpecialInfo');
    Route::post('/farmUser/clickWorldTree', 'App\Api\FarmUserController@clickWorldTree');
    Route::get('/farmShop/getList', 'App\Api\FarmShopController@getList');
    Route::post('/farmShop/buy', 'App\Api\FarmShopController@buy');
    Route::post('/farmWarehouse/getList', 'App\Api\FarmWarehouseController@getList');
    Route::post('/farmWarehouse/extend', 'App\Api\FarmWarehouseController@extendWarehouse');
    Route::post('/farmTask/getList', 'App\Api\FarmTaskController@getList');
    Route::post('/farmTask/submit', 'App\Api\FarmTaskController@submit');
    Route::post('/farmTask/cancel', 'App\Api\FarmTaskController@cancel');
});

Route::post('/appupdate/version', 'App\Api\AppUpdateController@version');

Route::post('/auth/email/sendCode', 'App\Api\Auth\EmailLoginController@sendEmailCode')->middleware(['limit_form_repeat:3']);
Route::post('/auth/email/loginEmail', 'App\Api\Auth\EmailLoginController@loginEmail')->middleware(['limit_form_repeat:3', 'email.login_code']);
Route::post('/auth/visitor/loginVisitor', 'App\Api\Auth\VisitorLoginController@loginVisitor')->middleware(['limit_form_repeat:3']);
Route::post('/auth/taptap/login', 'App\Api\Auth\TapTapLoginController@login')->middleware(['limit_form_repeat:3', 'taptap.token']);

Route::as('web3')->prefix('web3')->group(function () {
    Route::get('signature', 'App\Api\Auth\Web3LoginController@signature')->middleware('limit_form_repeat:3');
    Route::post('login', 'App\Api\Auth\Web3LoginController@login')->middleware(['limit_form_repeat:3', 'web3.signature']);
});
