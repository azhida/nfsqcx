<?php

Route::get('/', 'Index\LoginController@login');
Route::get('index', 'Index\LoginController@login');

// 前台
Route::group(['prefix' => 'index', 'namespace' => 'Index'], function()
{
    Route::get('/', 'LoginController@login');
    Route::get('login', 'LoginController@login');
    Route::post('login', 'LoginController@login');
    // 发送短信验证码
    Route::post('sms/send', 'SmsController@send');

    // 需要登录
    Route::group(['middleware' => ['index_login']], function () {
        Route::get('index', 'LoginController@index');
        Route::get('logout', 'LoginController@logout');

        // 上班打卡
        Route::get('activity/clockIn', 'ActivityController@clockIn');
        Route::post('activity/saveClockInData', 'ActivityController@saveClockInData');
        // 下班打卡
        Route::get('activity/clockOut', 'ActivityController@clockOut');
        Route::post('activity/saveClockOutData', 'ActivityController@saveClockOutData');

        // 获取 各项数据列表
        Route::post('activity/getSelectData', 'ActivityController@getSelectData');
        // 获取口味列表
        Route::post('activity/getFlavorList', 'ActivityController@getFlavorList');
        // 获取产品数据
        Route::post('activity/getUploadingData', 'ActivityController@getUploadingData');

        // 上传图片
        Route::post('activity/uploadClockInPic', 'ActivityController@uploadClockInPic');

    });
});

// 管理后台
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function()
{
    Route::get('login', 'LoginController@login');
    Route::post('login', 'LoginController@login');

    // 需要登录
    Route::group(['middleware' => ['admin_login']], function () {
        Route::get('index', 'IndexController@index');
        Route::get('welcome', 'IndexController@welcome');

        Route::get('adminList', 'AdminController@adminList');

        Route::post('openAdmin', 'AdminController@openAdmin');
    });


});

