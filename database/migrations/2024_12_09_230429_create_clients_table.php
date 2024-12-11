<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientsTable extends Migration {

	public function up()
	{
		Schema::create('clients', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('first_name');
			$table->string('second_name');
			$table->string('email');
			$table->string('phone');
			$table->date('brith_date')->nullable();
			$table->integer('city_id')->unsigned()->nullable();
			$table->enum('type', array('male', 'female'))->nullable();
			$table->string('api_token')->nullable();
			$table->string('pin_code')->nullable();
			$table->string('password');
		});
	}

	public function down()
	{
		Schema::drop('clients');
	}
}