<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointSystemSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_system_summaries', function (Blueprint $table) {
            $table->unsignedInteger('point_system_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->unsignedInteger('customer_group_id')->nullable();
            $table->integer('shift');
            $table->string('encoded_by');
            $table->decimal('equivalent_points');
            $table->timestamps();

            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('point_system_id')->references('id')->on('point_systems')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('point_system_summaries');
    }
}
