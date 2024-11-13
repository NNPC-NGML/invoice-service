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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->unsignedBigInteger('invoice_advice_id');
            $table->string('consumed_volume_amount_in_naira');
            $table->string('consumed_volume_amount_in_dollar');
            $table->string('dollar_to_naira_convertion_rate');
            $table->string('vat_amount');
            $table->string('total_volume_paid_for');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
