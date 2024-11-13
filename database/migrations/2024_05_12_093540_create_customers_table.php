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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string("company_name")->comment();
            $table->string('email')->unique()->comment();
            $table->string('phone_number')->comment();
            $table->string('password')->comment();
            $table->integer("created_by_user_id")->comment();
            $table->boolean('status')->default(0)->comment();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
