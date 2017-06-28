<?php

namespace Larrock\ComponentUsers;

use Illuminate\Support\ServiceProvider;

class LarrockComponentUsersServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'larrock');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/larrock'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app->make(UsersComponent::class);

        $migrations = [];
        $timestamp = date('Y_m_d_His', time());
        $timestamp_after = date('Y_m_d_His', time()+10);

        if ( !class_exists('CreateUsersTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_create_users_table.php'] =
                database_path('migrations/'.$timestamp.'_create_users_table.php');
        }
        if ( !class_exists('CreateRolesTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_create_roles_table.php'] =
                database_path('migrations/'.$timestamp.'_create_roles_table.php');
        }
        if ( !class_exists('CreatRoleUserTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_create_role_user_table.php'] =
                database_path('migrations/'.$timestamp.'_create_role_user_table.php');
        }
        if ( !class_exists('CreatePasswordResetsTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_create_password_resets_table.php'] =
                database_path('migrations/'.$timestamp.'_create_password_resets_table.php');
        }
        if ( !class_exists('CreatPermissionsTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_create_permissions_table.php'] =
                database_path('migrations/'.$timestamp.'_create_permissions_table.php');
        }
        if ( !class_exists('CreatePermissionRoleTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_create_permission_role_table.php'] =
                database_path('migrations/'.$timestamp.'_create_permission_role_table.php');
        }
        if ( !class_exists('CreatePermissionUserTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_create_permission_user_table.php'] =
                database_path('migrations/'.$timestamp.'_create_permission_user_table.php');
        }

        if ( !class_exists('AddForeignKeysToPermissionRoleTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_add_foreign_keys_to_permission_role_table.php'] =
                database_path('migrations/'.$timestamp_after.'_add_foreign_keys_to_permission_role_table.php');
        }
        if ( !class_exists('AddForeignKeysToPermissionUserTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_add_foreign_keys_to_permission_user_table.php'] =
                database_path('migrations/'.$timestamp_after.'_add_foreign_keys_to_permission_user_table.php');
        }
        if ( !class_exists('AddForeignKeysToRoleUserTable')){
            $migrations[__DIR__.'/database/migrations/0000_00_00_000000_add_foreign_keys_to_role_user_table.php'] =
                database_path('migrations/'.$timestamp_after.'_add_foreign_keys_to_role_user_table.php');
        }

        $this->publishes($migrations, 'migrations');
    }
}