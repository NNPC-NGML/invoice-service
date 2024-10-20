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
        Schema::create('invoice_advice', function (Blueprint $table) {
            $table->id();
            $table->boolean('with_vat');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('customer_site_id');
            $table->string('capex_recovery_amount');
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
        Schema::dropIfExists('invoice_advice');
    }
};
