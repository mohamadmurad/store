<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->integer('rate');
            $table->foreignId('user_id');
            $table->foreign('user_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('users');

            $table->foreignId('product_id');
            $table->foreign('product_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('products');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rates', function (Blueprint $table) {
            //
        });
    }
}
