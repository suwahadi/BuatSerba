<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Payment;
use Livewire\Component;

class OrderDetail extends Component
{
    public $order;

    public $orderNumber;

    public $paymentData = [];

    public function mount($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        $this->order = Order::with(['items.product.images', 'items.sku'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if (auth()->check() && $this->order->user_id !== auth()->id()) {
            abort(403);
        }
        $payment = Payment::where('order_id', $this->order->id)->first();
        if ($payment) {
            $this->paymentData = [
                'expired_at' => $payment->expired_at?->toIso8601String(),
                'transaction_status' => $payment->transaction_status,
            ];
        }
    }

    public function render()
    {
        return view('livewire.order-detail', [
            'order' => $this->order,
            'paymentData' => $this->paymentData,
        ])->layout('components.layouts.guest');
    }
}
