<?php

use Larrock\ComponentUsers\AdminUsersController;
use Larrock\ComponentUsers\UserController;
use Larrock\ComponentUsers\LoginController;
use Larrock\ComponentUsers\RegisterController;
use Larrock\ComponentUsers\ForgotPasswordController;
use Larrock\ComponentUsers\ResetPasswordController;

$middleware = ['web', 'GetSeo'];
if(file_exists(base_path(). '/vendor/fanamurov/larrock-menu')){
    $middleware[] = 'AddMenuFront';
}
if(file_exists(base_path(). '/vendor/fanamurov/larrock-blocks')){
    $middleware[] = 'AddBlocksTemplate';
}
if(file_exists(base_path(). '/vendor/fanamurov/larrock-discount')){
    $middleware[] = 'DiscountsShare';
}

Route::group(['middleware' => $middleware], function(){
    Route::get('/login', 'Larrock\ComponentUsers\UsersController@showLoginForm')->name('user.login');
    Route::post('/login', 'Larrock\ComponentUsers\UsersController@login')->name('user.login.post');

    Route::any('/logout', 'Larrock\ComponentUsers\UsersController@logout')->name('user.logout');
    Route::post('/register', 'Larrock\ComponentUsers\UsersController@register')->name('user.logout.post');

    Route::get('/user', 'Larrock\ComponentUsers\UsersController@index')->name('user.index');
    Route::get('/cabinet', 'Larrock\ComponentUsers\UsersController@cabinet')->name('user.cabiner');

    Route::get('password/reset', 'Larrock\ComponentUsers\UsersController@showPasswordRequestForm')->name('password.request');
    Route::post('password/email', 'Larrock\ComponentUsers\UsersController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Larrock\ComponentUsers\UsersController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset.post');
});

Route::group(['prefix' => 'admin', 'middleware'=> ['web', 'level:2', 'LarrockAdminMenu', 'SaveAdminPluginsData', 'SiteSearchAdmin']], function(){
    Route::resource('users', AdminUsersController::class);
});