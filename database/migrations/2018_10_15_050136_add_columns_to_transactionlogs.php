<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToTransactionlogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->decimal('liters');
            $table->decimal('price');
            $table->decimal('amount');
            $table->decimal('points');
            $table->integer('shift');
            $table->string('remarks')->nullable();
            $table->unsignedInteger('customer_group_id')->nullable();

            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->dropColumn('customer_id')->nullable();
            $table->dropColumn('product_id')->nullable();
            $table->dropColumn('liters');
            $table->dropColumn('price');
            $table->dropColumn('amount');
            $table->dropColumn('points');
            $table->dropColumn('shift');
            $table->dropColumn('remarks');
            $table->dropColumn('customer_group_id');
        });
    }
}
