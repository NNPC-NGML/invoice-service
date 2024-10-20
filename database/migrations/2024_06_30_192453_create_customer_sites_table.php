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
        Schema::create('customer_sites', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->integer("customer_id")->comment();
            $table->string("site_address")->comment();
            $table->integer("ngml_zone_id")->comment();
            $table->string("site_name")->comment();
            $table->string("phone_number")->comment();
            $table->string("email")->comment();
            $table->string("site_contact_person_name")->comment();
            $table->string("site_contact_person_email")->comment();
            $table->string("site_contact_person_phone_number")->comment();
            $table->string("site_contact_person_signature")->nullable()->comment();
            $table->boolean("site_existing_status")->default(0)->comment();
            $table->integer("created_by_user_id")->comment();
            $table->boolean("status")->comment();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_sites');
    }
};
