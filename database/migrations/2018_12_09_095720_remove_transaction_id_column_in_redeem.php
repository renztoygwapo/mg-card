<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveTransactionIdColumnInRedeem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('redeems', function($table) {
            $table->dropForeign('redeems_transaction_id_foreign');
            $table->dropColumn('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('redeems', function (Blueprint $table) {
            $table->unsignedInteger('transaction_id')->after('customer_id');

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });
    }
}
