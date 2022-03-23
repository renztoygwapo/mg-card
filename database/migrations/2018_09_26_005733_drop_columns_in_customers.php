<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnsInCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function($table) {
           $table->dropColumn('address_province');
           $table->dropColumn('address_brgy');
           $table->dropColumn('address_street');
           $table->dropColumn('address_block');
           $table->dropColumn('address_room');
       });
   }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('address_room')->nullable()->after('mobile_no');;
            $table->string('address_block')->nullable()->after('mobile_no');;
            $table->string('address_street')->nullable()->after('mobile_no');;
            $table->string('address_brgy')->after('mobile_no');;
            $table->string('address_province')->nullable()->after('mobile_no');;
        });
    }
}
