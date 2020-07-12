<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrateProductsOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers_products', function (Blueprint $table) {
            $table->foreignId('products_id');
            $table->foreign('products_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('products');

            $table->foreignId('offers_id');
            $table->foreign('offers_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('offers');




            $table->integer('quantity')->default(1);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_offers', function (Blueprint $table) {
            //
        });
    }
}
