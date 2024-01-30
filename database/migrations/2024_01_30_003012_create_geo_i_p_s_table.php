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
        Schema::create('geo_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip'); // Column to store IP address
            $table->string('country_code'); // Column to store country code
            $table->string('country_name'); // Column to store country name
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_ips');
    }
};
