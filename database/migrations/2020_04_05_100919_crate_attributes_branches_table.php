<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrateAttributesBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attributes_branches', function (Blueprint $table) {
            $table->foreignId('branches_id');
            $table->foreign('branches_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('branches');

            $table->foreignId('attributes_id');
            $table->foreign('attributes_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('attributes');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branches_attributes', function (Blueprint $table) {
            //
        });
    }
}
