<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->decimal('saleRate');
            $table->decimal('newPrice');
            $table->date('start');
            $table->date('end');
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('product_id');
            $table->foreign('product_id')->onDelete('cascade')->onUpdate('cascade')
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
        Schema::dropIfExists('sales');
    }
}
