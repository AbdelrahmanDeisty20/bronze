<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration {

	public function up()
	{
		Schema::create('orders', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('address_id')->unsigned()->nullable();
			$table->enum('state', array('pending', 'paid', 'unpaid'));
			$table->integer('shipping_id')->unsigned()->nullable();
			$table->integer('client_id')->unsigned();
			$table->decimal('total_price');
		});
	}

	public function down()
	{
		Schema::drop('orders');
	}
}