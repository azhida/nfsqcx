<?php

Route::get('/', 'Index\LoginController@login');
// 将 账户表 合并到 办事处表中
Route::get('mergeAccountToOffice', 'CommonController@mergeAccountToOffice');
Route::get('uploadSignImgToOss', 'CommonController@uploadSignImgToOss');
Route::get('downloadSignImgFromOss', 'CommonController@downloadSignImgFromOss');
Route::get('downloadProductImgFromOss', 'CommonController@downloadProductImgFromOss');
Route::any('downloadFileFromLocal', 'CommonController@downloadFileFromLocal');
Route::any('downloadFileToLocal', 'CommonController@downloadFileToLocal');
Route::get('updateSignData', 'CommonController@updateSignData');
Route::get('deleteSignData', 'CommonController@deleteSignData');
Route::get('excel/export','CommonController@export');
Route::get('excel/import','CommonController@import');

// 删除 oss 图片
Route::post('deleteOssFile', 'Controller@deleteOssFile');

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

        // 上传打卡照片
        Route::post('activity/uploadClockInAndOutPic', 'ActivityController@uploadClockInAndOutPic');

    });
});

// 管理后台
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function()
{
    Route::get('login', 'LoginController@login');
    Route::post('login', 'LoginController@login');

    // 需要登录
    Route::group(['middleware' => ['admin_login']], function () {

        Route::get('logout', 'LoginController@logout');

        Route::get('index', 'IndexController@index');
        Route::get('welcome', 'IndexController@welcome');

        // 管理员管理
        Route::get('adminList', 'AdminController@adminList');
        Route::get('adminAdd', 'AdminController@adminAdd');
        Route::post('adminAdd', 'AdminController@adminAdd');
        Route::get('adminEdit/{id?}', 'AdminController@adminEdit');
        Route::post('adminEdit', 'AdminController@adminEdit');
        Route::post('adminDelete', 'AdminController@adminDelete');

        Route::get('adminAole', 'AdminController@adminAole');

        Route::post('openAdmin', 'AdminController@openAdmin');

        // 角色管理
        Route::get('roleList', 'RoleController@roleList');
        Route::get('roleAdd', 'RoleController@roleAdd');
        Route::post('roleAdd', 'RoleController@roleAdd');
        Route::get('roleEdit/{id}', 'RoleController@roleEdit');
        Route::post('roleEdit', 'RoleController@roleEdit');
        Route::post('roleDetele', 'RoleController@roleDetele');

        // 权限管理
        Route::get('ruleList', 'RuleController@ruleList');
        Route::get('ruleAdd', 'RuleController@ruleAdd');
        Route::post('ruleAdd', 'RuleController@ruleAdd');
        Route::get('ruleEdit/{id}', 'RuleController@ruleEdit');
        Route::post('ruleEdit', 'RuleController@ruleEdit');
        Route::post('ruleDelete', 'RuleController@ruleDelete');
        // 权限排序（菜单排序）
        Route::post('changeSort', 'RuleController@changeSort');

        // 促销员管理
        Route::get('sellersList', 'SellersController@sellersList');
        Route::get('sellersAdd', 'SellersController@sellersAdd');
        Route::post('sellersAdd', 'SellersController@sellersAdd');
        Route::get('sellersEdit/{id}', 'SellersController@sellersEdit');
        Route::post('sellersEdit', 'SellersController@sellersEdit');
        Route::post('sellersDelete', 'SellersController@sellersDelete');

        // 办事处管理
        Route::get('officesList', 'OfficesController@officesList');
        Route::get('officesAdd', 'OfficesController@officesAdd');
        Route::post('officesAdd', 'OfficesController@officesAdd');
        Route::get('officesEdit/{id}', 'OfficesController@officesEdit');
        Route::post('officesEdit', 'OfficesController@officesEdit');
        Route::post('officesDelete', 'OfficesController@officesDelete');

        // 经销商管理
        Route::get('dealersList', 'DealersController@dealersList');
        Route::post('is_show_dealer', 'DealersController@isShow')->name('dealers.is_show_dealer');
        Route::get('dealersAdd', 'DealersController@dealersAdd');
        Route::post('dealersAdd', 'DealersController@dealersAdd');
        Route::get('dealersEdit/{id}', 'DealersController@dealersEdit');
        Route::post('dealersEdit', 'DealersController@dealersEdit');
        Route::post('dealersDelete', 'DealersController@dealersDelete');

        // 销售渠道管理
        Route::get('saleschannelList', 'SaleschannelController@saleschannelList');
        Route::get('saleschannelAdd', 'SaleschannelController@saleschannelAdd');
        Route::post('saleschannelAdd', 'SaleschannelController@saleschannelAdd');
        Route::get('saleschannelEdit/{id}', 'SaleschannelController@saleschannelEdit');
        Route::post('saleschannelEdit', 'SaleschannelController@saleschannelEdit');
        Route::post('saleschannelDelete', 'SaleschannelController@saleschannelDelete');

        // 产品分类管理
        Route::get('productCatList', 'ProductcatController@productCatList');
        Route::get('productCatAdd', 'ProductcatController@productCatAdd');
        Route::post('productCatAdd', 'ProductcatController@productCatAdd');
        Route::get('productCatEdit/{id}', 'ProductcatController@productCatEdit');
        Route::post('productCatEdit', 'ProductcatController@productCatEdit');
        Route::post('productCatDelete', 'ProductcatController@productCatDelete');

        // 产品口味管理
        Route::get('flavorList', 'FlavorController@flavorList');
        Route::get('flavorAdd', 'FlavorController@flavorAdd');
        Route::post('flavorAdd', 'FlavorController@flavorAdd');
        Route::get('flavorEdit/{id}', 'FlavorController@flavorEdit');
        Route::post('flavorEdit', 'FlavorController@flavorEdit');
        Route::post('flavorDelete', 'FlavorController@flavorDelete');

        Route::post('getFlavorListByCatId', 'FlavorController@getFlavorListByCatId');

        // 产品管理
        Route::get('productList', 'ProductController@productList');
        Route::get('productAdd', 'ProductController@productAdd');
        Route::post('productAdd', 'ProductController@productAdd');
        Route::get('productEdit/{id}', 'ProductController@productEdit');
        Route::post('productEdit', 'ProductController@productEdit');
        Route::post('productDelete', 'ProductController@productDelete');

        Route::get('uploadProductImgToOss', 'ProductController@uploadProductImgToOss');
        Route::post('uploadProductImgToOssOnlyOne', 'ProductController@uploadProductImgToOssOnlyOne'); // 将客户端上传图片保存到oss

        // 活动品项管理
        Route::get('activityitemList', 'ActivityitemController@activityitemList');
        Route::get('activityitemAdd', 'ActivityitemController@activityitemAdd');
        Route::post('activityitemAdd', 'ActivityitemController@activityitemAdd');
        Route::get('activityitemEdit/{id}', 'ActivityitemController@activityitemEdit');
        Route::post('activityitemEdit', 'ActivityitemController@activityitemEdit');
        Route::post('activityitemDelete', 'ActivityitemController@activityitemDelete');

        // 打卡管理
        Route::get('signclockinList', 'SignclockController@signclockinList'); // 上班打卡列表
        Route::get('signclockinDetail/{sign_clock_in_id}', 'SignclockController@signclockinDetail'); // 上班打卡详情
        Route::get('signclockoutList', 'SignclockController@signclockoutList'); // 下班打卡列表
        Route::get('signclockoutDetail/{sign_clock_out_id}', 'SignclockController@signclockoutDetail'); // 下班打卡详情
        Route::get('signclockList', 'SignclockController@signclockList'); // 上下班打卡列表
        Route::get('signclockDetail/{phone}/{date}', 'SignclockController@signclockDetail'); // 上下班打卡详情

        Route::post('signclockEdit/{id}', 'SignclockController@signclockEdit')->name('signclock.update'); // 编辑打卡信息
        Route::get('exportSignClockData', 'SignclockController@exportSignClockData');
        Route::post('exportSignClockData', 'SignclockController@exportSignClockData');

        Route::get('smsCodeList', 'SmsCodesController@index');


    });


});

