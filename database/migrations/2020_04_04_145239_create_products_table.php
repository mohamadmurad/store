<?php

use App\Products;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{

    protected $product_status = [
        products::AVAILABEL_PRODUCT,
        products::UNAVAILABEL_PRODUCT,
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('latinName');
            $table->string('code')->nullable();
            $table->unsignedInteger('quantity');
            $table->enum('status',$this->product_status);
            $table->integer('price');
            $table->text('details');
            $table->unsignedInteger('viewed')->default(0);
            $table->timestamps();
            $table->softDeletes();


            $table->foreignId('branch_id');
            $table->foreign('branch_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('branches');


            $table->foreignId('parent_id')->nullable();
            $table->foreign('parent_id')
                ->references('id')
                ->on('products');




            $table->foreignId('category_id')->nullable();
            $table->foreign('category_id')
                ->references('id')
                ->on('categories');



            $table->foreignId('group_id')->nullable();
            $table->foreign('group_id')->onDelete('cascade')->onUpdate('cascade')
                ->references('id')
                ->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
