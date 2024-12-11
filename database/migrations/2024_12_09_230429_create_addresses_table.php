<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAddressesTable extends Migration {

	public function up()
	{
		Schema::create('addresses', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('recipient_name');
			$table->string('cntry_region');
			$table->string('company_name')->nullable();
			$table->string('Identity')->nullable();
			$table->string('zip_code');
			$table->string('cntry_name');
			$table->string('phone');
		});
	}

	public function down()
	{
		Schema::drop('addresses');
	}
}