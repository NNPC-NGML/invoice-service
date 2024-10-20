<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_advice_list_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('customer_site_id');
            $table->string('volume');
            $table->string('inlet');
            $table->string('outlet');
            $table->string('take_or_pay_value');
            $table->string('allocation');
            $table->string('daily_target');
            $table->string('nomination');
            $table->unsignedBigInteger('daily_gas_id');
            $table->dateTime('date');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_advice_list_items');
    }
};
