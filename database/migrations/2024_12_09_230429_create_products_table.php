<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

	public function up()
	{
		Schema::create('products', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('name');
			$table->string('small_discrip');
			$table->string('description');
			$table->decimal('price');
			$table->string('color');
			$table->decimal('quantity');
			$table->bigInteger('sales_count')->unsigned()->nullable()->default('0');
			$table->integer('category_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('products');
	}
}