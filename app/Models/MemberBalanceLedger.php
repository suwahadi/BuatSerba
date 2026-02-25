<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberBalanceLedger extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'source_type',
        'source_id',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'reference_code',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related order when source_type is order-related.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'source_id');
    }

    /**
     * Get the related voucher when source_type is voucher-related.
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'source_id');
    }

    /**
     * Get the source model based on source_type.
     */
    public function source()
    {
        return match($this->source_type) {
            'order_payment', 'order_cancellation_refund' => $this->order(),
            'voucher_cashback' => $this->voucher(),
            default => null,
        };
    }

    public function scopeCredits(Builder $query): Builder
    {
        return $query->where('type', 'credit');
    }

    public function scopeDebits(Builder $query): Builder
    {
        return $query->where('type', 'debit');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
