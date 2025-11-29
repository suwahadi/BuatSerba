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
        Schema::table('skus', function (Blueprint $table) {
            $table->decimal('wholesale_price', 15, 2)->nullable()->after('selling_price');
            $table->integer('wholesale_min_quantity')->default(100)->after('wholesale_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skus', function (Blueprint $table) {
            $table->dropColumn(['wholesale_price', 'wholesale_min_quantity']);
        });
    }
};
