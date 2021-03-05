<?php

Route::get('categories', 'CategoryController@apiList');
Route::get('category/{category:slug}','CategoryController@apiCategory');
Route::get('category/{category:slug}/posts','NewsController@apiPosts');
Route::get('posts/top','NewsController@apiTopPosts');
Route::get('search','NewsController@apiSearch');
Route::get('posts/recent','NewsController@apiRecentPosts');
Route::get('settings','SettingsController@apiList');
Route::get('post/{news}','NewsController@apiPost');
Route::get('post/{news}/related','NewsController@apiPostRelated');
Route::get('user/{user:username}/posts','UserController@apiPosts');
