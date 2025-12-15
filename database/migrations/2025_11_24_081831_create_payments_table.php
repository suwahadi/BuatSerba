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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('payment_gateway')->default('midtrans');
            $table->string('transaction_id')->unique(); // dari Midtrans
            $table->timestamp('transaction_time');
            $table->string('transaction_status'); // pending, settlement, capture, deny, cancel, expire, refund
            $table->string('fraud_status')->nullable(); // accept, deny, challenge
            $table->string('payment_type'); // credit_card, bank_transfer, gopay, shopeepay, dll
            $table->string('payment_channel')->nullable(); // bca_va, bni_va, dll
            $table->decimal('gross_amount', 15, 2);
            $table->string('currency')->default('Rp');
            $table->text('signature_key')->nullable();
            $table->string('status_code')->nullable();
            $table->text('status_message')->nullable();
            $table->json('midtrans_response')->nullable(); // full response
            $table->text('snap_token')->nullable(); // token untuk Snap UI
            $table->text('snap_redirect_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->timestamps();

            $table->index('transaction_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
