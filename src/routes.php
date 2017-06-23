<?php

use Larrock\ComponentUsers\AdminUsersController;
use Larrock\ComponentUsers\UserController;
use Larrock\ComponentUsers\LoginController;

$middleware = ['web', 'GetSeo'];
if(file_exists(base_path(). '/vendor/fanamurov/larrock-menu')){
    $middleware[] = 'AddMenuFront';
}
if(file_exists(base_path(). '/vendor/fanamurov/larrock-blocks')){
    $middleware[] = 'AddBlocksTemplate';
}

Route::group(['middleware' => $middleware], function(){
    // Authentication routes...
    Route::get('login', LoginController::class .'@showLoginForm')->name('login');
    Route::post('login', LoginController::class .'@login');
    Route::post('logout', LoginController::class .'@logout')->name('logout');
    Route::get('/logout', LoginController::class .'@logout');

    // Registration Routes...
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');

    // Password Reset Routes...
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

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
    Route::post('/user/removeOrder/{id}', [
        'as' => 'user.removeOrder', 'uses' => UserController::class .'@removeOrder'
    ]);
});

Route::group(['prefix' => 'admin', 'middleware'=> ['web', 'level:2', 'LarrockAdminMenu']], function(){
    Route::resource('users', AdminUsersController::class);
});