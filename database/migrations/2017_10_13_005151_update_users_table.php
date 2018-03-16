<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table)
        {
            if ( !Schema::hasColumn('users', 'first_name')) {
                $table->char('first_name')->default('')->nullable();
            }
            if ( !Schema::hasColumn('users', 'last_name')) {
                $table->char('last_name')->default('')->nullable();
            }
            if ( !Schema::hasColumn('users', 'fio')) {
                $table->char('fio')->default('')->nullable();
            }
            if ( !Schema::hasColumn('users', 'address')) {
                $table->char('address')->default('')->nullable();
            }
            if ( !Schema::hasColumn('users', 'tel')) {
                $table->char('tel')->default('')->nullable();
            }
            if ( !Schema::hasColumn('users', 'permissions')) {
                $table->char('permissions')->default('')->nullable();
            }
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'first_name')) {
                $table->dropColumn('first_name');
            }
            if (Schema::hasColumn('users', 'last_name')) {
                $table->dropColumn('last_name');
            }
            if (Schema::hasColumn('users', 'fio')) {
                $table->dropColumn('fio');
            }
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('users', 'tel')) {
                $table->dropColumn('tel');
            }
            if (Schema::hasColumn('users', 'permissions')) {
                $table->dropColumn('permissions');
            }
        });
    }

}
