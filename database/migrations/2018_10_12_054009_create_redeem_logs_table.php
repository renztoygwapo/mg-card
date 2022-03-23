<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRedeemLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('redeem_logs', function (Blueprint $table) {
            $table->unsignedInteger('redeem_id');
            $table->string('encoded_by');
            $table->timestamps();

            $table->foreign('redeem_id')->references('id')->on('redeems')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('redeem_logs');
    }
}
