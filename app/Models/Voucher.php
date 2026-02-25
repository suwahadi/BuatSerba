<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'voucher_code',
        'voucher_name',
        'image',
        'type',
        'amount',
        'min_spend',
        'max_discount_amount',
        'user_id',
        'is_new_user_only',
        'usage_limit',
        'usage_count',
        'limit_per_user',
        'is_free_shipment',
        'is_active',
        'sort',
        'valid_start',
        'valid_end',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'min_spend' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'is_new_user_only' => 'boolean',
        'is_free_shipment' => 'boolean',
        'is_active' => 'boolean',
        'valid_start' => 'datetime',
        'valid_end' => 'datetime',
        'cashback_amount' => 'decimal:2',
        'cashback_percentage' => 'decimal:2',
    ];

    public function hasCashback(): bool
    {
        return $this->cashback_type === 'member_balance' && 
               ($this->cashback_amount > 0 || $this->cashback_percentage > 0);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
