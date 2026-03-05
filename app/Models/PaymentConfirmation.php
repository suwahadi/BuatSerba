<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentConfirmation extends Model
{
    protected $fillable = [
        'order_id',
        'nama_lengkap',
        'bank',
        'nomor_rekening',
        'bukti_transfer_path',
        'catatan',
        'confirmed_at',
        'is_read',
        'read_at',
        'is_validated',
        'validated_at',
        'validated_by',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_validated' => 'boolean',
        'validated_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'validated_by');
    }

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if (! $model->isDirty('is_read')) {
                return;
            }

            if ($model->is_read) {
                $model->read_at = $model->read_at ?? now();

                return;
            }

            $model->read_at = null;
        });
    }
}
