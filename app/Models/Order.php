<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($order) {
            // Check if status changed to 'completed' and payment method is member_balance
            if ($order->wasChanged('status') && $order->status === 'completed') {
                if ($order->payment_method === 'member_balance' && $order->user_id) {
                    try {
                        $memberWalletService = new \App\Services\MemberWalletService();
                        $memberWalletService->completeOrder($order);
                        
                        Log::info('Locked balance released for completed order', [
                            'order_number' => $order->order_number,
                            'user_id' => $order->user_id,
                            'total' => $order->total
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to release locked balance for completed order', [
                            'order_number' => $order->order_number,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        });
    }

    protected $fillable = [
        'order_number',
        'user_id',
        'session_id',
        'branch_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_province',
        'shipping_city',
        'shipping_district',
        'shipping_subdistrict',
        'shipping_postal_code',
        'shipping_address',
        'shipping_method',
        'shipping_service',
        'shipping_cost',
        'payment_method',
        'payment_status',
        'payment_deadline',
        'paid_at',
        'subtotal',
        'service_fee',
        'discount',
        'total',
        'status',
        'notes',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'shipping_cost' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'payment_deadline' => 'datetime',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // Helper methods
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    public function markAsPaid()
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'status' => 'processing',
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Get human-readable payment method label
     */
    public function getPaymentMethodLabel()
    {
        return \App\Enums\PaymentMethod::fromValue($this->payment_method)?->label() 
            ?? ucfirst(str_replace('_', ' ', $this->payment_method));
    }

    /**
     * Get order status enum instance
     */
    public function getOrderStatusEnum(): \App\Enums\OrderStatus
    {
        return \App\Enums\OrderStatus::tryFrom($this->status) ?? \App\Enums\OrderStatus::FAILED;
    }

    /**
     * Get order status label
     */
    public function getOrderStatusLabel(): string
    {
        return $this->getOrderStatusEnum()->label();
    }

    /**
     * Get order status badge classes (for Tailwind)
     */
    public function getOrderStatusBadgeClasses(): string
    {
        return $this->getOrderStatusEnum()->badgeClasses();
    }

    /**
     * Get payment status enum instance
     */
    public function getPaymentStatusEnum(): \App\Enums\PaymentStatus
    {
        return \App\Enums\PaymentStatus::tryFrom($this->payment_status) ?? \App\Enums\PaymentStatus::FAILED;
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabel(): string
    {
        return $this->getPaymentStatusEnum()->label();
    }

    /**
     * Get payment status short label (for badges)
     */
    public function getPaymentStatusShortLabel(): string
    {
        return $this->getPaymentStatusEnum()->shortLabel();
    }

    /**
     * Get payment status badge classes (for Tailwind)
     */
    public function getPaymentStatusBadgeClasses(): string
    {
        return $this->getPaymentStatusEnum()->badgeClasses();
    }

    /**
     * Update payment status from Midtrans notification
     */
    public function updatePaymentStatus($transactionStatus, $fraudStatus = null, $notificationData = [])
    {
        // Payment is considered paid when status is settlement/capture AND fraud_status is accept or null
        if (in_array($transactionStatus, ['settlement', 'capture']) && ($fraudStatus === 'accept' || $fraudStatus === null)) {
            $this->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'paid_at' => now(),
            ]);

            // Process cashback if voucher was used
            if ($this->voucher_code) {
                $voucher = \App\Models\Voucher::where('voucher_code', $this->voucher_code)->first();
                if ($voucher && $voucher->hasCashback() && $this->user_id) {
                    $voucherService = new \App\Services\VoucherService();
                    $voucherService->processCashback(
                        $this->user_id,
                        $voucher,
                        $this->subtotal,
                        $this->id
                    );
                }
            }
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $this->update([
                'payment_status' => 'failed',
                'status' => 'payment_failed',
            ]);

            // Release member balance if payment was made with member balance and is now expired/cancelled
            if ($this->user_id) {
                $payment = \App\Models\Payment::where('order_id', $this->id)
                    ->where('payment_gateway', 'member_balance')
                    ->first();
                
                if ($payment) {
                    $memberWalletService = new \App\Services\MemberWalletService();
                    $memberWalletService->releaseOrderLock(
                        $this->user_id,
                        $this->id,
                        'Pembayaran dibatalkan/expire untuk pesanan #' . $this->order_number
                    );
                }
            }
        }
    }
}
