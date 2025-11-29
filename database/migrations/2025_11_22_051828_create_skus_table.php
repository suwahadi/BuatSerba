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
        Schema::create('skus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->decimal('base_price', 15, 2);
            $table->decimal('selling_price', 15, 2);
            $table->integer('weight'); // gram
            $table->decimal('length', 8, 2)->nullable(); // cm
            $table->decimal('width', 8, 2)->nullable(); // cm
            $table->decimal('height', 8, 2)->nullable(); // cm
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skus');
    }
};
