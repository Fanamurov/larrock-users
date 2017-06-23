<?php

use Larrock\ComponentUsers\AdminUsersController;
use Larrock\ComponentUsers\UserController;

Route::group(['middleware' => ['web', 'AddMenuFront', 'GetSeo', 'AddBlocksTemplate']], function(){
    // Authentication routes...
    Route::auth();
    Route::get('/logout', 'Auth\LoginController@logout');

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