<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PremiumMembership extends Model
{
    protected $fillable = [
        'user_id',
        'price',
        'status',
        'payment_method',
        'payment_proof_path',
        'started_at',
        'expires_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
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
}
