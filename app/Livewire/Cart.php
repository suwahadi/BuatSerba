<?php

namespace App\Livewire;

use App\Models\CartItem;
use App\Models\FlashSaleItem;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Cart extends Component
{
    public $promoCode = '';

    public $discount = 0;

    public $shippingCost = 0;

    public $serviceFee = 0;

    protected $listeners = ['cartUpdated' => '$refresh'];

    public function mount()
    {
        // Ensure session has an ID for guest users
        if (! Session::has('cart_session_id')) {
            Session::put('cart_session_id', Session::getId());
        }

        $this->pruneExpiredFlashItems();

        // Restore voucher on initial load / reload
        if (Session::has('applied_voucher')) {
            $voucherData = Session::get('applied_voucher');
            $this->promoCode = $voucherData['code'];

            // Re-calculate strictly
            $voucherService = new \App\Services\VoucherService;
            // We need to fetch cart items first to get subtotal correctly here?
            // Computed properties are accessible.

            $result = $voucherService->applyVoucher(
                $voucherData['code'],
                $this->subtotal,
                auth()->user()
            );

            if ($result['success']) {
                $this->discount = $result['data']['discount_amount'];
                Session::put('applied_voucher', $result['data']);
            } else {
                // If invalid (e.g. expired while in session), clear it
                $this->discount = 0;
                Session::forget('applied_voucher');
            }
        }
    }

    public function getCartItemsProperty()
    {
        $sessionId = Session::get('cart_session_id');

        return CartItem::with(['product', 'sku', 'flashSaleItem.flashSale'])
            ->where(function ($query) use ($sessionId) {
                $query->where('session_id', $sessionId);
                if (auth()->check()) {
                    $query->orWhere('user_id', auth()->id());
                }
            })
            ->get();
    }

    /**
     * Drop cart rows whose flash entry is no longer valid (session ended,
     * sold out, or item removed). Per product spec: auto-remove + notify.
     */
    protected function pruneExpiredFlashItems(): void
    {
        $sessionId = Session::get('cart_session_id');

        $flashRows = CartItem::with('flashSaleItem.flashSale')
            ->whereNotNull('flash_sale_item_id')
            ->where(function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId);
                if (auth()->check()) {
                    $q->orWhere('user_id', auth()->id());
                }
            })
            ->get();

        $removed = [];

        foreach ($flashRows as $row) {
            $flashItem = $row->flashSaleItem;
            $invalid = ! $flashItem
                || ! $flashItem->flashSale
                || ! $flashItem->flashSale->isLive()
                || ($flashItem->stock_limit - $flashItem->sold_count) < $row->quantity;

            if ($invalid) {
                $removed[] = $row->product?->name ?? 'item';
                $row->delete();
            }
        }

        if (! empty($removed)) {
            $names = implode(', ', array_unique($removed));
            session()->flash('error', 'Flash Sale berakhir / stok habis. Item dihapus dari keranjang: '.$names);
            $this->dispatch('cartUpdated');
        }
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
        $cartItem = CartItem::with('flashSaleItem.flashSale')->find($cartItemId);

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

        $flashItem = $cartItem->flashSaleItem;
        if ($cartItem->flash_sale_item_id && $flashItem) {
            if (! $flashItem->flashSale || ! $flashItem->flashSale->isLive()) {
                session()->flash('error', 'Sesi Flash Sale berakhir.');
                $cartItem->delete();
                $this->dispatch('cartUpdated');

                return;
            }
            if (($flashItem->stock_limit - $flashItem->sold_count) < $quantity) {
                session()->flash('error', 'Kuota Flash Sale tidak mencukupi. Sisa: '.max(0, $flashItem->stock_limit - $flashItem->sold_count));

                return;
            }
        }

        // Update quantity and price (preserve flash_price when row is a flash item)
        $cartItem->quantity = $quantity;
        $cartItem->price = $flashItem
            ? (float) $flashItem->flash_price
            : $cartItem->sku->getPriceForQuantity($quantity);
        $cartItem->save();

        session()->flash('message', 'Jumlah item berhasil diupdate.');
        $this->dispatch('cartUpdated');
    }

    public function incrementQuantity($cartItemId)
    {
        $cartItem = CartItem::with('flashSaleItem.flashSale')->find($cartItemId);

        if (! $cartItem) {
            return;
        }

        $newQuantity = $cartItem->quantity + 1;

        if ($newQuantity > $cartItem->sku->stock_quantity) {
            session()->flash('error', 'Stok tidak mencukupi.');

            return;
        }

        $flashItem = $cartItem->flashSaleItem;
        if ($cartItem->flash_sale_item_id && $flashItem) {
            if (! $flashItem->flashSale || ! $flashItem->flashSale->isLive()) {
                session()->flash('error', 'Sesi Flash Sale berakhir.');
                $cartItem->delete();
                $this->dispatch('cartUpdated');

                return;
            }
            if (($flashItem->stock_limit - $flashItem->sold_count) < $newQuantity) {
                session()->flash('error', 'Kuota Flash Sale tidak mencukupi.');

                return;
            }
        }

        // Update quantity and price (preserve flash_price when row is a flash item)
        $cartItem->quantity = $newQuantity;
        $cartItem->price = $flashItem
            ? (float) $flashItem->flash_price
            : $cartItem->sku->getPriceForQuantity($newQuantity);
        $cartItem->save();

        $this->dispatch('cartUpdated');
    }

    public function decrementQuantity($cartItemId)
    {
        $cartItem = CartItem::with('flashSaleItem')->find($cartItemId);

        if (! $cartItem) {
            return;
        }

        $newQuantity = $cartItem->quantity - 1;

        if ($newQuantity < 1) {
            return;
        }

        // Update quantity and price (preserve flash_price when row is a flash item)
        $cartItem->quantity = $newQuantity;
        $cartItem->price = $cartItem->flashSaleItem
            ? (float) $cartItem->flashSaleItem->flash_price
            : $cartItem->sku->getPriceForQuantity($newQuantity);
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

        CartItem::where(function ($query) use ($sessionId) {
            $query->where('session_id', $sessionId);
            if (auth()->check()) {
                $query->orWhere('user_id', auth()->id());
            }
        })
            ->delete();

        session()->flash('message', 'Keranjang berhasil dikosongkan.');
        $this->dispatch('cartUpdated');
    }

    public function applyPromoCode()
    {
        $voucherService = new \App\Services\VoucherService;
        $code = strtoupper($this->promoCode);

        $result = $voucherService->applyVoucher(
            $code,
            $this->subtotal,
            auth()->user()
        );

        if ($result['success']) {
            $data = $result['data'];
            $this->discount = $data['discount_amount'];

            // Store to session for Checkout page
            Session::put('applied_voucher', $data);

            if (!empty($data['is_free_shipment'])) {
                 $this->dispatch('notify', message: 'Voucher Gratis Ongkir berhasil digunakan!', type: 'success');
            } else {
                 $this->dispatch('notify', message: $result['message'], type: 'success');
            }
        } else {
            $this->discount = 0;
            Session::forget('applied_voucher');
            $this->dispatch('notify', message: $result['message'], type: 'error');
        }
    }

    public function removePromoCode()
    {
        $this->promoCode = '';
        $this->discount = 0;
        Session::forget('applied_voucher');
        session()->flash('message', 'Kode voucher dihapus.');
    }

    // Recalculate voucher logic AFTER actions (like update qty) are performed
    public function dehydrate()
    {
        // Re-prune flash-sale rows after any in-page action (qty change, etc.)
        $this->pruneExpiredFlashItems();

        if (Session::has('applied_voucher')) {
            $voucherData = Session::get('applied_voucher');

            // Re-calculate based on NEW subtotal (after quantity updates)
            $voucherService = new \App\Services\VoucherService;
            $result = $voucherService->applyVoucher(
                $voucherData['code'],
                $this->subtotal,
                auth()->user()
            );

            if ($result['success']) {
                $this->discount = $result['data']['discount_amount'];
                // Update session with new calculation if needed
                if ($this->discount !== $voucherData['discount_amount']) {
                    $voucherData['discount_amount'] = $this->discount;
                    Session::put('applied_voucher', $voucherData);
                }
            } else {
                // Voucher invalid now (e.g. subtotal changed below minimum)
                $this->discount = 0;
                Session::forget('applied_voucher');
                $this->promoCode = '';
                // Optional: flash error only if it was previously valid?
                // session()->flash('error', 'Voucher tidak lagi valid.');
            }
        }
    }

    public function calculateShipping()
    {
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
