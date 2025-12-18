<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class OrderDetail extends Component
{
    public $order;

    public $orderNumber;

    public function mount($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        // Load order by order number with all necessary relationships
        $this->order = Order::with(['items.product.images', 'items.sku'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Security check: Ensure the order belongs to the current user if logged in
        if (auth()->check() && $this->order->user_id !== auth()->id()) {
            abort(403);
        }

        // For guest users, we rely on the unique order number (code) which acts as a token
    }

    public function render()
    {
        return view('livewire.order-detail', [
            'order' => $this->order,
        ])->layout('components.layouts.guest');
    }
}
