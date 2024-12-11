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
        Schema::create('gcc_approved_by_customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->integer('customer_id');
            $table->integer('gcc_id');
            $table->integer('customer_site_id');
            $table->string('signature');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gcc_approved_by_customers');
    }
};
