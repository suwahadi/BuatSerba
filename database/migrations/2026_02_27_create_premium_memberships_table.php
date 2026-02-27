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
        Schema::create('premium_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 12, 2)->default(100000); // Rp100.000
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');
            $table->string('payment_method')->default('bank_transfer'); // manual upload bukti
            $table->string('payment_proof_path')->nullable(); // path ke file bukti transfer
            $table->timestamp('started_at')->nullable(); // waktu member premium aktif
            $table->timestamp('expires_at')->nullable(); // waktu member premium hangus
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('premium_memberships');
    }
};
