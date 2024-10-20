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
        Schema::create('ngml_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('bank_address');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('sort_code');
            $table->string('tin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngml_accounts');
    }
};
