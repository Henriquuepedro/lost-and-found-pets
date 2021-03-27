<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->decimal('gross_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('fee_amount', 10, 2);
            $table->decimal('net_amount', 10, 2);
            $table->decimal('extra_amount', 10, 2);
            $table->string('coupon_name', 256);
            $table->integer('installment_count');
            $table->integer('user_id');
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
        Schema::dropIfExists('order_payments');
    }
}
