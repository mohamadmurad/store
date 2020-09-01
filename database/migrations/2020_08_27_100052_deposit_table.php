<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DepositTable extends Migration
{ /**
 * Run the migrations.
 *
 * @return void
 */
    public function up()
    {
        Schema::create('deposit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreign('user_id')->references('id')->on('users');


            $table->foreignId('cards_id');
            $table->foreign('cards_id')->references('id')->on('cards');

            $table->unsignedInteger('amount');

            $table->unsignedInteger('cost');

            $table->dateTime('depositDate');

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
        Schema::dropIfExists('deposit');
    }
}
