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
            $table->char('first_name')->default('');
            $table->char('last_name')->default('');
            $table->char('fio')->default('');
            $table->text('address');
            $table->char('tel')->default('');
            $table->text('permissions');
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
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('fio');
            $table->dropColumn('address');
            $table->dropColumn('tel');
            $table->dropColumn('permissions');
        });
	}

}
