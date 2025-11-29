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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label'); // rumah, kantor, dll
            $table->string('recipient_name');
            $table->string('phone');
            $table->integer('province_id'); // dari Rajaongkir
            $table->string('province_name');
            $table->integer('city_id'); // dari Rajaongkir
            $table->string('city_name');
            $table->string('city_type'); // Kabupaten/Kota
            $table->integer('subdistrict_id')->nullable(); // dari Rajaongkir
            $table->string('subdistrict_name')->nullable();
            $table->string('postal_code');
            $table->text('full_address');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
