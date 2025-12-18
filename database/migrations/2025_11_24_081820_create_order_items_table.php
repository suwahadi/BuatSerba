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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sku_id')->constrained()->cascadeOnDelete();

            $table->string('product_name'); // Snapshot
            $table->string('sku_code'); // Snapshot
            $table->json('sku_attributes')->nullable(); // Snapshot of variant attributes

            $table->integer('quantity');
            $table->decimal('price', 15, 2); // Price at time of order
            $table->decimal('subtotal', 15, 2); // quantity * price

            $table->timestamps();

            // Index for performance
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
