<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrateAttributeValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->string('value');
            $table->foreignId('attributes_id');
            $table->foreign('attributes_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('attributes');

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
        Schema::dropIfExists('attribute_values');
    }
}
