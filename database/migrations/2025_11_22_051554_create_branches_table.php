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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // BDG001, JKT001
            $table->string('name'); // Cabang Bandung, Cabang Jakarta
            $table->string('phone');
            $table->string('email');
            $table->integer('province_id'); // untuk Rajaongkir
            $table->string('province_name');
            $table->integer('city_id'); // untuk Rajaongkir
            $table->string('city_name');
            $table->string('city_type');
            $table->integer('subdistrict_id')->nullable();
            $table->string('subdistrict_name')->nullable();
            $table->string('postal_code');
            $table->text('full_address');
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // untuk sorting cabang terdekat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
