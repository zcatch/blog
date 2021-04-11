<?php

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

//前台路由
Route::get('/','Home\IndexController@index')->name('index');
Route::get('/index','Home\IndexController@index')->name('index');
Route::get('lists/{id}','Home\IndexController@lists');
Route::get('detail/{id}','Home\IndexController@detail');

//邮箱注册激活路由
Route::get('emailregister','Home\RegisterController@register');
Route::post('doregister','Home\RegisterController@doRegister');

//收藏
Route::post('collect','Home\IndexController@collect');
//评论
Route::post('comment','Home\IndexController@comment');

//邮箱注册激活路由
Route::get('emailregister','Home\RegisterController@register');
Route::post('doregister','Home\RegisterController@doRegister');

Route::get('active','Home\RegisterController@active');
Route::get('forget','Home\RegisterController@forget');
//发送密码找回邮件
Route::post('doforget','Home\RegisterController@doforget');
//重新设置密码页面
Route::get('reset','Home\RegisterController@reset');
//重置密码逻辑
Route::post('doreset','Home\RegisterController@doreset');


//登录
Route::get('login','Home\LoginController@login');
Route::post('dologin','Home\LoginController@doLogin');
Route::get('loginout','Home\LoginController@loginOut');


//手机注册页路由
Route::get('phoneregister','Home\RegisterController@phoneReg');
//发送手机验证码
Route::get('sendcode','Home\RegisterController@sendCode');
Route::post('dophoneregister','Home\RegisterController@doPhoneRegister');

Route::group(['prefix'=>'admin','namespace'=>'Admin'],function(){
    Route::get('login','LoginController@login');
    Route::post('doLogin','LoginController@doLogin');
    Route::get('code','LoginController@code');
    Route::get('jm','LoginController@jm');
});

Route::get('noaccess','Admin\LoginController@noaccess');
Route::group(['prefix'=>'admin','namespace'=>'Admin','middleware'=>['isLogin','hasRole']],function(){
    Route::get('index','LoginController@index');
    Route::get('welcome','LoginController@welcome');
    Route::get('logout','LoginController@logout');

    Route::resource('user','UserController');
    Route::post('user/del','UserController@delAll');

    Route::resource('role','RoleController');
    Route::post('role/del','RoleController@delAll');

    Route::resource('permission','PermissionController');
    Route::post('permission/del','PermissionController@delAll');

    Route::resource('cate','CateController');
    Route::post('cate/changeorder','CateController@changeOrder');

    Route::resource('article','ArticleController');
    Route::post('article/upload','ArticleController@upload');
    Route::post('article/pre_mk','ArticleController@pre_mk');
    Route::post('article/recommend','ArticleController@recommend');

    Route::resource('config','ConfigController');
    Route::post('config/changecontent','ConfigController@changeContent');

});


//Route::get('/code/captcha/{temp}', 'Admin\LoginController@captcha');