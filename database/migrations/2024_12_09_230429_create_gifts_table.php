<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGiftsTable extends Migration {

	public function up()
	{
		Schema::create('gifts', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('gift');
		});
	}

	public function down()
	{
		Schema::drop('gifts');
	}
}