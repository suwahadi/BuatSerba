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
     * Update membership from Midtrans notification.
     *
     * midtrans_response is MERGED, not replaced — webhook payloads omit qr_string
     * / actions[] from the initial /charge response, which the QRIS view needs.
     */
    public function updateFromMidtransNotification(array $notification): void
    {
        $existingResponse = is_array($this->midtrans_response) ? $this->midtrans_response : [];
        $mergedResponse = array_merge($existingResponse, $notification);

        \Log::info('PremiumMembership::updateFromMidtransNotification', [
            'membership_id' => $this->id,
            'existing_has_qr_string' => isset($existingResponse['qr_string']),
            'notif_has_qr_string' => isset($notification['qr_string']),
            'merged_has_qr_string' => isset($mergedResponse['qr_string']),
        ]);

        $this->update([
            'transaction_status' => $notification['transaction_status'] ?? $this->transaction_status,
            'fraud_status' => $notification['fraud_status'] ?? $this->fraud_status,
            'status_code' => $notification['status_code'] ?? $this->status_code,
            'status_message' => $notification['status_message'] ?? $this->status_message,
            'signature_key' => $notification['signature_key'] ?? $this->signature_key,
            'midtrans_response' => $mergedResponse,
        ]);

        $this->updateStatusFromPayment();
    }

    /**
     * Update membership status based on payment status.
     *
     * For renewals: the new row's expires_at is stacked on top of the user's
     * latest still-valid membership. Previously the calculation fell back to
     * now() because the new row's own expires_at was null, which silently reset
     * the renewal duration instead of extending it.
     */
    protected function updateStatusFromPayment(): void
    {
        if (in_array($this->transaction_status, ['settlement', 'capture']) &&
            ($this->fraud_status === 'accept' || $this->fraud_status === null)) {

            $durationDays = config('premium_membership.duration_days', 365);

            $base = $this->expires_at;
            if (!$base) {
                $latestActiveExpiry = static::query()
                    ->where('user_id', $this->user_id)
                    ->where('id', '!=', $this->id)
                    ->where('status', 'active')
                    ->whereNotNull('expires_at')
                    ->where('expires_at', '>', now())
                    ->max('expires_at');

                $base = $latestActiveExpiry
                    ? \Carbon\Carbon::parse($latestActiveExpiry)
                    : now();
            }

            $this->update([
                'status' => 'active',
                'paid_at' => now(),
                'started_at' => $this->started_at ?? now(),
                'expires_at' => $base->copy()->addDays($durationDays),
            ]);

            \Log::info('PremiumMembership activated', [
                'membership_id' => $this->id,
                'user_id' => $this->user_id,
                'base_expiry' => $base->toDateString(),
                'new_expires_at' => $this->fresh()->expires_at?->toDateString(),
                'duration_days' => $durationDays,
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
