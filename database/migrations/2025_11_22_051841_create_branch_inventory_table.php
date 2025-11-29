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
        Schema::create('branch_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sku_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity_available')->default(0);
            $table->integer('quantity_reserved')->default(0);
            $table->integer('minimum_stock_level')->default(0);
            $table->integer('reorder_point')->default(0);
            $table->timestamps();

            $table->unique(['branch_id', 'sku_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_inventory');
    }
};
