<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrateFavoriteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favorite', function (Blueprint $table) {
            $table->foreignId('user_id');
            $table->foreign('user_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('users');

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
        Schema::dropIfExists('favorite');
    }
}
