<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            if (!Schema::hasColumn('vouchers', 'min_spend')) {
                $table->decimal('min_spend', 12, 2)->default(0)->after('amount');
            }
            if (!Schema::hasColumn('vouchers', 'max_discount_amount')) {
                $table->decimal('max_discount_amount', 12, 2)->nullable()->after('min_spend');
            }
            if (!Schema::hasColumn('vouchers', 'is_new_user_only')) {
                $table->boolean('is_new_user_only')->default(false)->after('user_id');
            }
            if (!Schema::hasColumn('vouchers', 'usage_limit')) {
                $table->integer('usage_limit')->nullable()->after('is_new_user_only');
            }
            if (!Schema::hasColumn('vouchers', 'usage_count')) {
                $table->integer('usage_count')->default(0)->after('usage_limit');
            }
            if (!Schema::hasColumn('vouchers', 'limit_per_user')) {
                $table->integer('limit_per_user')->default(1)->after('usage_count');
            }
        });

        try {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->index(['voucher_code', 'is_active', 'valid_start', 'valid_end'], 'vouchers_search_idx');
            });
        } catch (\Exception $e) {
        }

        if (Schema::hasColumn('vouchers', 'type')) {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
        
        Schema::table('vouchers', function (Blueprint $table) {
            $table->enum('type', ['fixed', 'percentage'])->default('fixed')->after('image');
        });

        try {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] != 1091) {
                throw $e;
            }
        }

        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn([
                'min_spend', 
                'max_discount_amount', 
                'is_new_user_only', 
                'usage_limit', 
                'usage_count', 
                'limit_per_user'
            ]);
            
            $table->dropIndex('vouchers_search_idx');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('vouchers', function (Blueprint $table) {
            $table->enum('type', ['percentage', 'number'])->default('number')->after('image');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }
};
