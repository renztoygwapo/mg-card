<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToRedeemlogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('redeem_logs', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->after('redeem_id');
            $table->integer('amount')->after('customer_id');
            $table->integer('shift')->after('amount');
            $table->string('serial_no')->unique()->after('shift');

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('redeem_logs', function (Blueprint $table) {
            $table->dropColumn('customer_id');
            $table->dropColumn('amount');
            $table->dropColumn('shift');
            $table->dropColumn('serial_no');
        });
    }
}
