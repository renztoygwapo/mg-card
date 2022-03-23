<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCustomersAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('customer_group_id')->unsigned()->after('mobile_no');
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('cascade');
            $table->string('address_room')->nullable()->after('mobile_no');;
            $table->string('address_block')->nullable()->after('mobile_no');;
            $table->string('address_street')->nullable()->after('mobile_no');;
            $table->string('address_brgy')->after('mobile_no');;
            $table->string('address_province')->nullable()->after('mobile_no');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('customer_group_id');
            $table->dropColumn('address_room');
            $table->dropColumn('address_block');
            $table->dropColumn('address_street');
            $table->dropColumn('address_brgy');
            $table->dropColumn('address_province');
        });
    }
}
