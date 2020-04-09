<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('src');
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('products_id');
            $table->foreign('products_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('products');

            $table->foreignId('attachmentType_id');
            $table->foreign('attachmentType_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('attachment_types');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
}
