<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberWallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'locked_balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'locked_balance' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(MemberBalanceLedger::class, 'user_id', 'user_id');
    }

    public function getInitialBalanceAttribute(): float
    {
        // Get the balance before the last debit transaction
        $lastDebitLedger = $this->ledgers()
            ->where('type', 'debit')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($lastDebitLedger) {
            return (float) $lastDebitLedger->balance_before;
        }
        
        // If no debit transactions, get from first credit transaction or current balance
        $firstCreditLedger = $this->ledgers()
            ->where('type', 'credit')
            ->orderBy('created_at', 'asc')
            ->first();
            
        if ($firstCreditLedger) {
            return (float) $firstCreditLedger->balance_before;
        }
        
        // If no transactions, initial balance is current balance
        return (float) $this->balance;
    }

    public function getAvailableBalanceAttribute(): float
    {
        return (float) $this->balance;
    }
}
