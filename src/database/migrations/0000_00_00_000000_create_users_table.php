<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->default('');
			$table->char('email', 155)->unique();
			$table->string('password');
			$table->string('remember_token', 100)->nullable();
			$table->timestamps();
			$table->char('first_name')->default('')->nullable();
			$table->char('last_name')->default('')->nullable();
			$table->char('fio')->default('')->nullable();
			$table->text('address')->nullable();
			$table->char('tel')->default('')->nullable();
			$table->text('permissions')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
