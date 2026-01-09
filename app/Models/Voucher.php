<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'voucher_name',
        'image',
        'voucher_code',
        'valid_start',
        'valid_end',
        'type',
        'amount',
        'user_id',
        'is_free_shipment',
        'is_active',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'valid_start' => 'datetime',
            'valid_end' => 'datetime',
            'amount' => 'decimal:2',
            'is_free_shipment' => 'boolean',
            'is_active' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
