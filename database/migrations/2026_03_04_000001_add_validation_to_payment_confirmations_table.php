<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_confirmations', function (Blueprint $table) {
            $table->boolean('is_validated')->default(false)->index()->after('read_at');
            $table->dateTime('validated_at')->nullable()->after('is_validated');
            $table->unsignedBigInteger('validated_by')->nullable()->after('validated_at');
            $table->foreign('validated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payment_confirmations', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['is_validated', 'validated_at', 'validated_by']);
        });
    }
};
