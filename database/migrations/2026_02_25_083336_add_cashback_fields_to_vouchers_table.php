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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->enum('cashback_type', ['none', 'member_balance'])->default('none')->after('amount');
            $table->decimal('cashback_amount', 12, 2)->default(0)->after('cashback_type');
            $table->decimal('cashback_percentage', 5, 2)->default(0)->after('cashback_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['cashback_type', 'cashback_amount', 'cashback_percentage']);
        });
    }
};
