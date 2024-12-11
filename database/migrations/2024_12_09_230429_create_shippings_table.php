<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShippingsTable extends Migration {

	public function up()
	{
		Schema::create('shippings', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('name');
			$table->decimal('cost');
			$table->string('time');
		});
	}

	public function down()
	{
		Schema::drop('shippings');
	}
}