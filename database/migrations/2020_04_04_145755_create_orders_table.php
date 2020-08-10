<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->decimal('discount');
            $table->decimal('delevareAmount');


            $table->foreignId('user_id');
            $table->foreign('user_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('users');


            $table->foreignId('coupon_id')->nullable();
            $table->foreign('coupon_id')
                ->references('id')
                ->on('coupons');


            $table->foreignId('branch_id');
            $table->foreign('branch_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('branches');

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
        Schema::dropIfExists('orders');
    }
}
