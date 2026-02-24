<?php

namespace App\Models;

use App\Services\InventoryService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_gateway',
        'transaction_id',
        'transaction_time',
        'transaction_status',
        'fraud_status',
        'payment_type',
        'payment_channel',
        'gross_amount',
        'currency',
        'signature_key',
        'status_code',
        'status_message',
        'midtrans_response',
        'snap_token',
        'snap_redirect_url',
        'paid_at',
        'expired_at',
        'refunded_at',
        'refund_amount',
    ];

    protected function casts(): array
    {
        return [
            'transaction_time' => 'datetime',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
            'refunded_at' => 'datetime',
            'gross_amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'midtrans_response' => 'array',
        ];
    }

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function notifications()
    {
        return $this->hasMany(PaymentNotification::class);
    }

    /**
     * Update payment from Midtrans notification
     */
    public function updateFromMidtransNotification(array $notification)
    {
        $this->update([
            'transaction_status' => $notification['transaction_status'] ?? $this->transaction_status,
            'fraud_status' => $notification['fraud_status'] ?? $this->fraud_status,
            'status_code' => $notification['status_code'] ?? $this->status_code,
            'status_message' => $notification['status_message'] ?? $this->status_message,
            'signature_key' => $notification['signature_key'] ?? $this->signature_key,
            'midtrans_response' => $notification,
        ]);

        // Update order status based on payment status
        if ($this->order) {
            $this->updateOrderStatusFromPayment();
        }
    }

    /**
     * Update order status based on payment status
     */
    protected function updateOrderStatusFromPayment()
    {
        if (in_array($this->transaction_status, ['settlement', 'capture']) && ($this->fraud_status === 'accept' || $this->fraud_status === null)) {
            $this->order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'paid_at' => now(),
            ]);

            $branchId = (int) ($this->order->branch_id ?? 1);
            $inventoryService = new InventoryService;
            foreach ($this->order->items as $item) {
                $inventoryService->commit($branchId, (int) $item->sku_id, (int) $item->quantity);
            }
        } elseif (in_array($this->transaction_status, ['deny', 'cancel', 'expire', 'expired'])) {
            $this->order->update([
                'payment_status' => 'failed',
                'status' => 'payment_failed',
            ]);

            $branchId = (int) ($this->order->branch_id ?? 1);
            $inventoryService = new InventoryService;
            foreach ($this->order->items as $item) {
                $inventoryService->release($branchId, (int) $item->sku_id, (int) $item->quantity);
            }
        }
    }

    public function isSuccessful(): bool
    {
        return in_array($this->transaction_status, ['settlement', 'capture']) &&
               ($this->fraud_status === 'accept' || $this->fraud_status === null);
    }

    public function isPending(): bool
    {
        return $this->transaction_status === 'pending';
    }

    public function isFailed(): bool
    {
        return in_array($this->transaction_status, ['deny', 'cancel', 'expire', 'expired']);
    }

    public function isExpired(): bool
    {
        return in_array($this->transaction_status, ['expire', 'expired']);
    }
}
