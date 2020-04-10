<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->integer('balance')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('company_id');
            $table->foreign('company_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('companies');



            $table->foreignId('user_id')->unique();
            $table->foreign('user_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');
    }
}
