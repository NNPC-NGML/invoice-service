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
        Schema::create('gccs', function (Blueprint $table) {
            $table->id();
            $table->boolean('with_vat')->default(false)->nullable();
            $table->integer('customer_id');
            $table->integer('customer_site_id');
            $table->string('capex_recovery_amount');
            $table->timestamp('gcc_date');
            $table->integer('department_id');
            $table->integer('gcc_created_by');
            $table->integer('letter_id')->default(1)->nullable();
            $table->integer('status')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gccs');
    }
};
