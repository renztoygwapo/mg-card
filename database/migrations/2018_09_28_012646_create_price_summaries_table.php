<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_summaries', function (Blueprint $table) {
            $table->unsignedInteger('price_id')->nullable();
            $table->date('product_date');
            $table->integer('shift');
            $table->string('encoded_by');
            $table->unsignedInteger('product_id')->nullable();
            $table->decimal('updated_product_price');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('price_id')->references('id')->on('prices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_summaries');
    }
}
