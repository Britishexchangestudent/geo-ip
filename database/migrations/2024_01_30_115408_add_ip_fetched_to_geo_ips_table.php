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
        Schema::table('geo_ips', function (Blueprint $table) {
            $table->boolean('ip_fetched')->default(false)->after('country_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('geo_ips', function (Blueprint $table) {
            $table->dropColumn('ip_fetched');
        });
    }
};
