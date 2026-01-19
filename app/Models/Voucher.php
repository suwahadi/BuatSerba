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

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'min_spend' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'is_new_user_only' => 'boolean',
            'usage_limit' => 'integer',
            'usage_count' => 'integer',
            'limit_per_user' => 'integer',
            'is_free_shipment' => 'boolean',
            'is_active' => 'boolean',
            'sort' => 'integer',
            'valid_start' => 'datetime',
            'valid_end' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
