<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePromosCustomerAvail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_customer_avails', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('promo_id')->nullable();
            $table->string('isAvail');
            $table->date('date_expired');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('promo_id')->references('id')->on('promo_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promo_customer_avails');
    }
}
