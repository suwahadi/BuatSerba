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
            // Check if columns already exist before adding them
            if (!Schema::hasColumn('skus', 'pricing_tiers')) {
                // Add JSON column for dynamic pricing tiers
                $table->json('pricing_tiers')->nullable()->after('wholesale_min_quantity');
            }
            
            if (!Schema::hasColumn('skus', 'use_dynamic_pricing')) {
                // Add a flag to enable/disable dynamic pricing
                $table->boolean('use_dynamic_pricing')->default(false)->after('pricing_tiers');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skus', function (Blueprint $table) {
            if (Schema::hasColumn('skus', 'pricing_tiers')) {
                $table->dropColumn('pricing_tiers');
            }
            
            if (Schema::hasColumn('skus', 'use_dynamic_pricing')) {
                $table->dropColumn('use_dynamic_pricing');
            }
        });
    }
};