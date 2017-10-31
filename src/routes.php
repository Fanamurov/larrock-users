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
    Route::post('register', RegisterController::class .'@register');

    Route::post('logout', LoginController::class .'@logout')->name('logout');
    Route::get('/logout', LoginController::class .'@logout');

    // Password Reset Routes...
    Route::get('password/reset', ForgotPasswordController::class .'@showLinkRequestForm')->name('password.request');
    Route::post('password/email', ForgotPasswordController::class .'@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', ResetPasswordController::class .'@showResetForm')->name('password.reset');
    Route::post('password/reset', ResetPasswordController::class .'@reset');

    Route::get(
        '/socialite/{provider}', [
            'as' => 'socialite.auth',
            function ( $provider ) {
                return \Socialite::driver( $provider )->redirect();
            }
        ]
    );

    Route::get('/socialite/{provider}/callback', [
        'as' => 'socialite', 'uses' => UserController::class .'@socialite'
    ]);

    Route::get('/login', [
        'as' => 'user.index', 'uses' => UserController::class .'@index'
    ]);
    Route::get('/user', [
        'as' => 'user.index', 'uses' => UserController::class .'@index'
    ]);
    Route::get('/user/cabinet', [
        'as' => 'user.cabinet', 'uses' => UserController::class .'@cabinet'
    ]);
    Route::post('/user/login', [
        'as' => 'user.login', 'uses' => UserController::class .'@authenticate'
    ]);
    Route::get('/user/logout', [
        'as' => 'user.logout', 'uses' => UserController::class .'@logout'
    ]);
    Route::post('/user/edit', [
        'as' => 'user.edit', 'uses' => UserController::class .'@updateProfile'
    ]);
});

Route::group(['prefix' => 'admin', 'middleware'=> ['web', 'level:2', 'LarrockAdminMenu', 'SaveAdminPluginsData', 'SiteSearchAdmin']], function(){
    Route::resource('users', AdminUsersController::class);
});