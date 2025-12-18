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
        // Check if table already exists before creating it
        if (! Schema::hasTable('order_shipping_info')) {
            Schema::create('order_shipping_info', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->string('recipient_name');
                $table->string('phone');
                $table->integer('province_id');
                $table->string('province_name');
                $table->integer('city_id');
                $table->string('city_name');
                $table->string('city_type');
                $table->integer('subdistrict_id')->nullable();
                $table->string('subdistrict_name')->nullable();
                $table->string('postal_code');
                $table->text('full_address');
                $table->string('courier_code');
                $table->string('courier_service');
                $table->text('courier_service_description')->nullable();
                $table->string('estimated_delivery_days');
                $table->decimal('shipping_cost', 15, 2);
                $table->string('tracking_number')->nullable();
                $table->timestamp('shipped_at')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shipping_info');
    }
};
