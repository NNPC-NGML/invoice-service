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
            $table->unsignedBigInteger('invoice_advice_id')->nullable();
            $table->unsignedBigInteger('daily_volume_id');
            $table->string('volume');
            $table->string('inlet')->nullable();
            $table->string('outlet')->nullable();
            $table->string('take_or_pay_value')->nullable();
            $table->string('allocation')->nullable();
            $table->string('daily_target')->nullable();
            $table->string('nomination')->nullable();
            $table->timestamp('original_date');
            $table->integer('gcc_id');
            $table->integer('status')->default(0);
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
