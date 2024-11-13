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
        Schema::create('daily_volumes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("customer_id")->comment('customer id');
            $table->bigInteger("customer_site_id")->comment('customer site id');
            $table->float('volume')->comment('volume in Scf');
            $table->string('remark')->nullable()->comment('remark, if any');
            // $table->float('rate')->comment('rate in NGN/Scf, should be picked from current rate in settings');
            // $table->float('amount')->comment('amount from (volume * rate) in NGN');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_volumes');
    }
};
