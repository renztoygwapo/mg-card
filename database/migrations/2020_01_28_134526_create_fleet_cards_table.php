<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFleetCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fleet_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name');
            $table->string('company_address');
            $table->string('company_number');
            $table->bigInteger('tin_no');
            $table->unsignedInteger('customer_group_id')->nullable();
            $table->timestamps();

            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fleet_cards');
    }
}
