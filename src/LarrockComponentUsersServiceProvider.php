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
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

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
    }
}