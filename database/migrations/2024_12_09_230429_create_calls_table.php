<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCallsTable extends Migration {

	public function up()
	{
		Schema::create('calls', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('number');
		});
	}

	public function down()
	{
		Schema::drop('calls');
	}
}