<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAuthenticatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_authenticates', function(Blueprint $table) {
			$table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->string('network')->index();
            $table->string('uid')->index();
            $table->string('identity')->index();
            $table->string('profile');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_authenticates');
	}

}
