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
        Schema::table('user_addresses', function (Blueprint $table) {
            // Drop columns: label, recipient_name, phone
            $table->dropColumn(['label', 'recipient_name', 'phone']);

            // Add columns: district_id, district_name (before subdistrict_id)
            $table->integer('district_id')->after('city_type');
            $table->string('district_name')->after('district_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            // Restore the dropped columns
            $table->string('label');
            $table->string('recipient_name');
            $table->string('phone');

            // Remove the added columns
            $table->dropColumn(['district_id', 'district_name']);
        });
    }
};
