<?php

Route::view('/login', 'login')->name('login');
Route::post('/login', 'AuthController@login');
