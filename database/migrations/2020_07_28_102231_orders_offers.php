<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrdersOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers_orders', function (Blueprint $table) {
            $table->integer('quantity')->default(1);


            $table->foreignId('orders_id');
            $table->foreign('orders_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('orders');

            $table->foreignId('offers_id');
            $table->foreign('offers_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('offers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders_offers');
    }
}
