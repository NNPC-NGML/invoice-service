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
            $table->float('volume')->comment('volume in mscf');
            $table->float('inlet_pressure')->comment('inlet pressure in psi')->nullable();
            $table->float('outlet_pressure')->comment('outlet pressure in psi')->nullable();
            $table->float('allocation')->comment('allocation in MMscfd')->nullable();
            $table->float('nomination')->comment('nomination in MMscfd')->nullable();
            $table->integer('status')->comment('volume status')->default(0);
            $table->integer('created_by')->comment('who entered the record');
            $table->string('remark')->comment('volume remark')->nullable();
            $table->integer('approved_by')->default(0)->comment('who entered the record');
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
