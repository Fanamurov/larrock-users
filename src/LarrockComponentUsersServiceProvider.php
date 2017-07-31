<?php

namespace Larrock\ComponentUsers;

use Illuminate\Support\ServiceProvider;
use Larrock\ComponentUsers\Facades\LarrockUsers;

class LarrockComponentUsersServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/views', 'larrock');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/larrock')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('larrockusers', function() {
            $class = config('larrock.components.users', UsersComponent::class);
            return new $class;
        });

        $migrations = [];
        $timestamp = date('Y_m_d_His', time());
        $timestamp_after = date('Y_m_d_His', time()+10);

        if ( !class_exists('CreateUsersTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_create_users_table.php'] =
                database_path('migrations/2015_01_14_111111_create_users_table.php');
        }else{
            if ( !class_exists('UpdateUsersTable')){
                $migrations[__DIR__.'/database/migrations/0000_00_00_000000_update_users_table.php'] =
                    database_path('migrations/'. $timestamp .'_update_users_table.php');
            }
        }
        if ( !class_exists('CreatePasswordResetsTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_create_password_resets_table.php'] =
                database_path('migrations/'.$timestamp.'_create_password_resets_table.php');
        }

        $this->publishes($migrations, 'migrations');
    }
}