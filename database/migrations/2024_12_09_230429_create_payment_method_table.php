<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentMethodTable extends Migration {

	public function up()
	{
		Schema::create('payment_method', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('card_number')->nullable();
			$table->string('name')->nullable();
			$table->date('end_date')->nullable();
			$table->string('security_code')->nullable();
            $table->string('status');
            $table->decimal('amount',8,2);
            $table->string('currency');
            $table->string('transaction_id')->nullable();
			$table->integer('cart_id')->unsigned();
			$table->integer('address_id')->unsigned();
			$table->integer('shipping_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('payment_method');
	}
}
