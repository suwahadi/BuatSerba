<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flash_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flash_sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sku_id')->constrained('skus')->restrictOnDelete();
            $table->decimal('flash_price', 12, 2);
            $table->decimal('original_price_snapshot', 12, 2)->nullable();
            $table->unsignedInteger('stock_limit');
            $table->unsignedInteger('sold_count')->default(0);
            $table->integer('sort')->default(0);
            $table->timestamps();

            $table->unique(['flash_sale_id', 'sku_id']);
            $table->index('sku_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flash_sale_items');
    }
};
