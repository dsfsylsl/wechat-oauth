<?php

use Illuminate\Support\Facades\Route;

Route::get('/wechat/oauth/access', 'OauthController@access');
Route::get('/wechat/oauth/callback', 'OauthController@callback')->name('wechatOauthCallback');
Route::get('/wechat/oauth/test', 'OauthController@test');
