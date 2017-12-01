<?php

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

Route::group(['prefix' => 'admin'], function(){
    Route::resource('users', 'Larrock\ComponentUsers\AdminUsersController');
});

Breadcrumbs::register('admin.'. LarrockUsers::getName() .'.index', function($breadcrumbs){
    $breadcrumbs->push(LarrockUsers::getTitle(), '/admin/'. LarrockUsers::getName());
});

Breadcrumbs::register('admin.'. LarrockUsers::getName() .'.create', function($breadcrumbs)
{
    $breadcrumbs->parent('admin.'. LarrockUsers::getName() .'.index');
    $breadcrumbs->push('Создание');
});

Breadcrumbs::register('admin.users.edit', function($breadcrumbs, $data)
{
    $breadcrumbs->parent('admin.'. LarrockUsers::getName() .'.index');
    $breadcrumbs->push($data->email);
});