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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_name', 191)->index();
            $table->string('image', 2048)->nullable();
            $table->string('voucher_code', 64)->unique()->index();
            $table->datetime('valid_start')->nullable()->index();
            $table->datetime('valid_end')->nullable()->index();
            $table->enum('type', ['percentage', 'number'])->default('number')->index();
            $table->decimal('amount', 12, 2)->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->boolean('is_free_shipment')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort')->default(0)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
