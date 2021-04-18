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

Auth::routes();
Route::get('/' , 'ReviewController@index')->name('index');
Route::get('/show/{id}', 'ReviewController@show')->name('show');

// ログインしている人だけがアクセスできるルーティンググループ
Route::group(['middleware' => 'auth'], function () {
	Route::get('/review', 'ReviewController@create')->name('create');
	Route::post('/review/store', 'ReviewController@store')->name('store');


//追加
Route::get('/like', 'LikeController@index'); // 👈 ブラウザでアクセスする
Route::get('/ajax/like/user_list', 'LikeController@user_list'); // 👈 ユーザー情報を取得
Route::post('/ajax/like', 'LikeController@like'); // 👈 いいね！データを追加

Route::post('/review/like', 'ReviewController@like')->name('like');

});

Route::get('/home', 'HomeController@index')->name('home');