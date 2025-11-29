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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->index();
            
            // Customer Information
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            
            // Shipping Address
            $table->string('shipping_province');
            $table->string('shipping_city');
            $table->string('shipping_district');
            $table->string('shipping_postal_code');
            $table->text('shipping_address');
            
            // Shipping Information
            $table->string('shipping_method');
            $table->string('shipping_service')->nullable();
            $table->decimal('shipping_cost', 15, 2)->default(0);
            
            // Payment Information
            $table->string('payment_method');
            $table->string('payment_status')->default('pending'); // pending, paid, failed, expired
            $table->timestamp('payment_deadline')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Order Amounts
            $table->decimal('subtotal', 15, 2);
            $table->decimal('service_fee', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            
            // Order Status
            $table->string('status')->default('pending'); // pending, processing, shipped, delivered, cancelled
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('payment_status');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
