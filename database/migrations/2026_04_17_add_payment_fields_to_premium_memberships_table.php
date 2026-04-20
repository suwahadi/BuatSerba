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
        Schema::table('premium_memberships', function (Blueprint $table) {
            $table->string('payment_gateway')->nullable()->after('payment_method');
            $table->string('transaction_id')->unique()->nullable()->after('payment_gateway');
            $table->timestamp('transaction_time')->nullable()->after('transaction_id');
            $table->string('transaction_status')->default('pending')->after('transaction_time');
            $table->string('fraud_status')->nullable()->after('transaction_status');
            $table->string('payment_type')->nullable()->after('fraud_status');
            $table->string('payment_channel')->nullable()->after('payment_type');
            $table->json('midtrans_response')->nullable()->after('payment_channel');
            $table->text('signature_key')->nullable()->after('midtrans_response');
            $table->string('status_code')->nullable()->after('signature_key');
            $table->text('status_message')->nullable()->after('status_code');
            $table->timestamp('paid_at')->nullable()->after('expires_at');
            
            $table->index('transaction_id');
            $table->index('transaction_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('premium_memberships', function (Blueprint $table) {
            $table->dropIndex(['transaction_id']);
            $table->dropIndex(['transaction_status']);
            $table->dropColumn([
                'payment_gateway',
                'transaction_id',
                'transaction_time',
                'transaction_status',
                'fraud_status',
                'payment_type',
                'payment_channel',
                'midtrans_response',
                'signature_key',
                'status_code',
                'status_message',
                'paid_at',
            ]);
        });
    }
};
