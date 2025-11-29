<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_gateway',
        'transaction_id',
        'transaction_time',
        'transaction_status',
        'fraud_status',
        'payment_type',
        'payment_channel',
        'gross_amount',
        'currency',
        'signature_key',
        'status_code',
        'status_message',
        'midtrans_response',
        'snap_token',
        'snap_redirect_url',
        'paid_at',
        'expired_at',
        'refunded_at',
        'refund_amount',
    ];

    protected function casts(): array
    {
        return [
            'transaction_time' => 'datetime',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
            'refunded_at' => 'datetime',
            'gross_amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'midtrans_response' => 'array',
        ];
    }

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function notifications()
    {
        return $this->hasMany(PaymentNotification::class);
    }

    /**
     * Update payment from Midtrans notification
     */
    public function updateFromMidtransNotification(array $notification)
    {
        $this->update([
            'transaction_status' => $notification['transaction_status'] ?? $this->transaction_status,
            'fraud_status' => $notification['fraud_status'] ?? $this->fraud_status,
            'status_code' => $notification['status_code'] ?? $this->status_code,
            'status_message' => $notification['status_message'] ?? $this->status_message,
            'signature_key' => $notification['signature_key'] ?? $this->signature_key,
            'midtrans_response' => $notification,
            'paid_at' => ($notification['transaction_status'] ?? null) === 'settlement' ? now() : $this->paid_at,
        ]);

        // Update order status based on payment status
        if ($this->order) {
            $this->updateOrderStatusFromPayment();
        }
    }

    /**
     * Update order status based on payment status
     */
    protected function updateOrderStatusFromPayment()
    {
        if ($this->transaction_status === 'settlement' && $this->fraud_status === 'accept') {
            $this->order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'paid_at' => now(),
            ]);
        } elseif (in_array($this->transaction_status, ['deny', 'cancel', 'expire'])) {
            $this->order->update([
                'payment_status' => 'failed',
                'status' => 'payment_failed',
            ]);
        }
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return $this->transaction_status === 'settlement' && $this->fraud_status === 'accept';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->transaction_status === 'pending';
    }

    /**
     * Check if payment has failed
     */
    public function isFailed(): bool
    {
        return in_array($this->transaction_status, ['deny', 'cancel', 'expire']);
    }

    /**
     * Check if payment is expired
     */
    public function isExpired(): bool
    {
        return $this->transaction_status === 'expire';
    }
}