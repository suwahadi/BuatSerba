<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'session_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_province',
        'shipping_city',
        'shipping_district',
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

    public function items()
    {
        return $this->hasMany(OrderItem::class);
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
     * Update payment status from Midtrans notification
     */
    public function updatePaymentStatus($transactionStatus, $fraudStatus = null, $notificationData = [])
    {
        if (($transactionStatus === 'settlement' || $transactionStatus === 'capture') && $fraudStatus === 'accept') {
            $this->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'paid_at' => now(),
            ]);
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $this->update([
                'payment_status' => 'failed',
                'status' => 'payment_failed',
            ]);
        }
    }
}