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
Route::get('api/:version/product', 'api/:version.Product/getRecent');
Route::get('api/:version/product/by_category/:id', 'api/:version.Product/getAllInCategory');

//category
Route::get('api/:version/category/all', 'api/:version.Category/getAllCategories');

//token
Route::post('api/:version/token/user', 'api/:version.Token/getToken');
