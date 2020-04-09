<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrateOrdersProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_products', function (Blueprint $table) {
            $table->integer('quantity')->default(1);


            $table->foreignId('orders_id');
            $table->foreign('orders_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('orders');

            $table->foreignId('products_id');
            $table->foreign('products_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders_products', function (Blueprint $table) {
            //
        });
    }
}
