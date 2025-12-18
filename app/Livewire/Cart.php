<?php

namespace App\Livewire;

use App\Models\CartItem;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Cart extends Component
{
    public $promoCode = '';

    public $discount = 0;

    public $shippingCost = 0;

    public $serviceFee = 2000;

    protected $listeners = ['cartUpdated' => '$refresh'];

    public function mount()
    {
        // Ensure session has an ID for guest users
        if (! Session::has('cart_session_id')) {
            Session::put('cart_session_id', Session::getId());
        }
    }

    public function getCartItemsProperty()
    {
        $sessionId = Session::get('cart_session_id');

        return CartItem::with(['product', 'sku'])
            ->where('session_id', $sessionId)
            ->when(auth()->check(), function ($query) {
                $query->orWhere('user_id', auth()->id());
            })
            ->get();
    }

    public function getSubtotalProperty()
    {
        return $this->cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    public function getTotalProperty()
    {
        return $this->subtotal + $this->serviceFee - $this->discount;
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        $cartItem = CartItem::find($cartItemId);

        if (! $cartItem) {
            session()->flash('error', 'Item tidak ditemukan di keranjang.');

            return;
        }

        // Validate quantity
        if ($quantity < 1) {
            $quantity = 1;
        }

        // Check stock availability
        if ($quantity > $cartItem->sku->stock_quantity) {
            session()->flash('error', 'Stok tidak mencukupi. Tersedia: '.$cartItem->sku->stock_quantity);

            return;
        }

        // Update quantity and price
        $cartItem->quantity = $quantity;
        $cartItem->price = $cartItem->sku->getPriceForQuantity($quantity);
        $cartItem->save();

        session()->flash('message', 'Jumlah item berhasil diupdate.');
        $this->dispatch('cartUpdated');
    }

    public function incrementQuantity($cartItemId)
    {
        $cartItem = CartItem::find($cartItemId);

        if (! $cartItem) {
            return;
        }

        $newQuantity = $cartItem->quantity + 1;

        if ($newQuantity > $cartItem->sku->stock_quantity) {
            session()->flash('error', 'Stok tidak mencukupi.');

            return;
        }

        // Update quantity and price
        $cartItem->quantity = $newQuantity;
        $cartItem->price = $cartItem->sku->getPriceForQuantity($newQuantity);
        $cartItem->save();

        $this->dispatch('cartUpdated');
    }

    public function decrementQuantity($cartItemId)
    {
        $cartItem = CartItem::find($cartItemId);

        if (! $cartItem) {
            return;
        }

        $newQuantity = $cartItem->quantity - 1;

        if ($newQuantity < 1) {
            return;
        }

        // Update quantity and price
        $cartItem->quantity = $newQuantity;
        $cartItem->price = $cartItem->sku->getPriceForQuantity($newQuantity);
        $cartItem->save();

        $this->dispatch('cartUpdated');
    }

    public function removeItem($cartItemId)
    {
        $cartItem = CartItem::find($cartItemId);

        if ($cartItem) {
            $cartItem->delete();
            session()->flash('message', 'Item berhasil dihapus dari keranjang.');
            $this->dispatch('cartUpdated');
        }
    }

    public function clearCart()
    {
        $sessionId = Session::get('cart_session_id');

        CartItem::where('session_id', $sessionId)
            ->when(auth()->check(), function ($query) {
                $query->orWhere('user_id', auth()->id());
            })
            ->delete();

        session()->flash('message', 'Keranjang berhasil dikosongkan.');
        $this->dispatch('cartUpdated');
    }

    public function applyPromoCode()
    {
        // Simple promo code validation (you can expand this)
        $promoCodes = [
            'DISKON10' => 10, // 10% discount
            'DISKON50K' => 50000, // Rp 50.000 discount
            'WELCOME' => 5, // 5% discount
        ];

        $code = strtoupper($this->promoCode);

        if (isset($promoCodes[$code])) {
            if ($promoCodes[$code] < 100) {
                // Percentage discount
                $this->discount = ($this->subtotal * $promoCodes[$code]) / 100;
            } else {
                // Fixed amount discount
                $this->discount = $promoCodes[$code];
            }

            session()->flash('message', 'Kode promo berhasil diterapkan!');
        } else {
            $this->discount = 0;
            session()->flash('error', 'Kode promo tidak valid.');
        }
    }

    public function calculateShipping()
    {
        // Simple shipping calculation based on subtotal
        $this->shippingCost = 25000; // Standard shipping
    }

    public function checkout()
    {
        if ($this->cartItems->isEmpty()) {
            session()->flash('error', 'Keranjang belanja Anda kosong.');

            return;
        }

        return redirect()->route('checkout');
    }

    public function render()
    {
        $this->calculateShipping();

        return view('livewire.cart', [
            'cartItems' => $this->cartItems,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
        ])->layout('components.layouts.guest');
    }
}
