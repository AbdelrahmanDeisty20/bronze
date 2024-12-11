<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCartsTable extends Migration {

	public function up()
	{
		Schema::create('carts', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('client_id')->unsigned();
			$table->integer('product_id')->unsigned();
			$table->integer('quantity');
			$table->decimal('total_price');
			$table->integer('order_id')->unsigned();
			$table->decimal('price');
		});
	}

	public function down()
	{
		Schema::drop('carts');
	}
}