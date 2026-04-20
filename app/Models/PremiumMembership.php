<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PremiumMembership extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'price',
        'status',
        'payment_method',
        'payment_proof_path',
        'started_at',
        'expires_at',
        'payment_gateway',
        'transaction_id',
        'transaction_time',
        'transaction_status',
        'fraud_status',
        'payment_type',
        'payment_channel',
        'midtrans_response',
        'signature_key',
        'status_code',
        'status_message',
        'paid_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'transaction_time' => 'datetime',
        'paid_at' => 'datetime',
        'midtrans_response' => 'array',
    ];

    /**
     * Get the user that owns this premium membership.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this membership is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->started_at && 
               $this->started_at->isPast() && 
               (!$this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Check if this membership is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->expires_at && $this->expires_at->isPast());
    }

    /**
     * Get days remaining for this membership.
     */
    public function daysRemaining(): ?int
    {
        if (!$this->isActive() || !$this->expires_at) {
            return null;
        }
        
        return now()->diffInDays($this->expires_at, false);
    }

    /**
     * Update membership from Midtrans notification
     */
    public function updateFromMidtransNotification(array $notification): void
    {
        $this->update([
            'transaction_status' => $notification['transaction_status'] ?? $this->transaction_status,
            'fraud_status' => $notification['fraud_status'] ?? $this->fraud_status,
            'status_code' => $notification['status_code'] ?? $this->status_code,
            'status_message' => $notification['status_message'] ?? $this->status_message,
            'signature_key' => $notification['signature_key'] ?? $this->signature_key,
            'midtrans_response' => $notification,
        ]);

        $this->updateStatusFromPayment();
    }

    /**
     * Update membership status based on payment status
     */
    protected function updateStatusFromPayment(): void
    {
        if (in_array($this->transaction_status, ['settlement', 'capture']) && 
            ($this->fraud_status === 'accept' || $this->fraud_status === null)) {
            
            $durationDays = config('premium_membership.duration_days', 365);
            
            $this->update([
                'status' => 'active',
                'paid_at' => now(),
                'started_at' => $this->started_at ?? now(),
                'expires_at' => ($this->expires_at ?? now())->addDays($durationDays),
            ]);

        } elseif (in_array($this->transaction_status, ['deny', 'cancel', 'expire', 'expired'])) {
            $this->update([
                'status' => 'cancelled',
            ]);
        }
    }

    /**
     * Check if payment is successful
     */
    public function isPaymentSuccessful(): bool
    {
        return in_array($this->transaction_status, ['settlement', 'capture']) &&
               ($this->fraud_status === 'accept' || $this->fraud_status === null);
    }

    /**
     * Check if payment is pending
     */
    public function isPaymentPending(): bool
    {
        return $this->transaction_status === 'pending';
    }

    /**
     * Check if payment is failed
     */
    public function isPaymentFailed(): bool
    {
        return in_array($this->transaction_status, ['deny', 'cancel', 'expire', 'expired']);
    }
}
