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

Route::view('/', 'index')->name('index');

Route::post('/logout', 'AuthController@logout')->name('logout');

Route::resource('category', 'CategoryController', ['except' => 'show']);

Route::post('/news/upload','NewsController@uploadImage');
Route::post('/news/upload-2','NewsController@uploadImageDropzone');
Route::post('/news/{news}/unlock','NewsController@unlock')->name('news_unlock');
Route::patch('/news/{news}/restore','NewsController@restore');
Route::patch('/news/{news}/ready','NewsController@ready')->name('news.ready');

Route::resource('news', 'NewsController');

Route::get('profile', 'UserController@edit')->name('user.edit');
Route::put('update', 'UserController@update')->name('user.update');

Route::get('user/{user}/permissions/edit', 'UserController@editPermission')->name('user.permission-edit');
Route::put('user/{user}/permissions/edit', 'UserController@updatePermission');

Route::get('settings', 'SettingsController@edit')->name('settings');
Route::get('about-us', 'SettingsController@editAbout')->name('about-us');
Route::get('banner', 'SettingsController@editBanner')->name('banner');
Route::put('settings', 'SettingsController@update');

Route::get('stats','SettingsController@stats')->name('stats');
Route::get('popular','SettingsController@popular')->name('popular');

Route::view('notifications','notifications')->name('notifications');

Route::resource('user', 'UserController', ['except' => ['edit', 'update']]);

Route::patch('notification','UserController@clearNotifications')->name('clear-notifications');
