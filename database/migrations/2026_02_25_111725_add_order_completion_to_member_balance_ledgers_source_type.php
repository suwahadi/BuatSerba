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
        Schema::table('member_balance_ledgers', function (Blueprint $table) {
            $table->enum('source_type', ['voucher_cashback', 'order_payment', 'order_cancellation_refund', 'admin_credit', 'admin_debit', 'order_completion'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_balance_ledgers', function (Blueprint $table) {
            $table->enum('source_type', ['voucher_cashback', 'order_payment', 'order_cancellation_refund', 'admin_credit', 'admin_debit'])->change();
        });
    }
};
