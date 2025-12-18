<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'order_id',
        'transaction_status',
        'notification_body',
        'processed',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'notification_body' => 'array',
            'processed' => 'boolean',
            'processed_at' => 'datetime',
        ];
    }

    // Relationships
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
