<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variant_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_one')->nullable();
            $table->foreignId('product_variant_two')->nullable();
            $table->foreignId('product_variant_three')->nullable();
            $table->foreign('product_variant_one')->references('id')->on('product_variants')->cascadeOnDelete();
            $table->foreign('product_variant_two')->references('id')->on('product_variants')->cascadeOnDelete();
            $table->foreign('product_variant_three')->references('id')->on('product_variants')->cascadeOnDelete();
            $table->double('price');
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_variant_prices', function (Blueprint $table){
            $table->dropForeign(['product_variant_one']);
            $table->dropForeign(['product_variant_two']);
            $table->dropForeign(['product_variant_three']);
            $table->dropForeign(['product_id']);
        });
        Schema::dropIfExists('product_variant_prices');
    }
}
