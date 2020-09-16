<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

//Route::get('test', 'test/Index/index');
//Banner
Route::get('api/:version/banner/:id','api/:version.Banner/getBanner');


//theme
Route::get('api/:version/theme', 'api/:version.Theme/getSimpleList');

Route::get('api/:version/theme/:id', 'api/:version.Theme/getComplexOne');

//product
//Route::get('api/:version/product/:id', 'api/:version.Product/getOne', [], ['id' => '\d+']);
//Route::get('api/:version/product/recent', 'api/:version.Product/getRecent');
//Route::get('api/:version/product/by_category/:id', 'api/:version.Product/getAllInCategory');

//|-------------------------------------------------------------------------
//| 路由分组
//|-------------------------------------------------------------------------
//Route::group('api/:version/product', function () {
//    Route::get('/:id', 'api/:version.Product/getOne', [], ['id' => '\d+']);
//    Route::get('/recent', 'api/:version.Product/getRecent');
//    Route::get('/by_category/:id', 'api/:version.Product/getAllInCategory');
//});
Route::group('api/:version/product', function () {
    Route::get('/:id', '/getOne', [], ['id' => '\d+']);
    Route::get('/recent', '/getRecent');
    Route::get('/by_category/:id', '/getAllInCategory');
}, ['prefix' => 'api/:version.Product']);

//address
Route::post('api/:version/address', 'api/:version.Address/createAndUpdateAddress');

//category
Route::get('api/:version/category/all', 'api/:version.Category/getAllCategories');

//token
Route::post('api/:version/token/user', 'api/:version.Token/getToken');

//order
Route::post('api/:version/order', 'api/:version.Order/placeOrder');
Route::get('api/:version/order/by_user', 'api/:version.Order/getSummaryByUser');
Route::get('api/:version/order/:id', 'api/:version.Order/getDetail', [], ['id' => '\d+']);

//pay
Route::post('api/:version/pay/pre_order', 'api/:version.Pay/getPreOrder');
Route::post('api/:version/pay/notify', 'api/:version.Pay/receiveNotify');//异步回调
Route::post('api/:version/pay/re_notify', 'api/:version.Pay/redirectNotify');//异步回调(用于xdebug调试)