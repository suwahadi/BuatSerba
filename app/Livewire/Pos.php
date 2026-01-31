<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sku;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Pos extends Component
{
    public $selectedProduct = null;

    public $price = 0;

    public $quantity = 1;

    public $customerName = '';

    public $customerEmail = '';

    public $customerPhone = '';

    public $selectedCustomerId = null;

    public $customerSearch = '';

    public $items = [];

    public $discount = 0;

    public $searchResults = [];

    protected $rules = [
        'customerName' => 'required|string|max:255',
        'customerEmail' => 'nullable|email|max:255',
        'customerPhone' => 'nullable|string|max:20',
    ];

    public function mount()
    {
        $this->items = [];
        $this->searchResults = [];
    }

    public function updatedCustomerSearch($value): void
    {
        if (empty($value) || strlen($value) < 1) {
            $this->searchResults = [];

            return;
        }

        $this->searchResults = User::query()
            ->where('role', 'regular')
            ->where('is_guest', false)
            ->where(function ($q) use ($value) {
                $q->where('name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ])->toArray();
    }

    public function selectCustomer(int $userId): void
    {
        $user = User::find($userId);
        if ($user && $user->role === 'regular') {
            $this->selectedCustomerId = $user->id;
            $this->customerName = $user->name;
            $this->customerEmail = $user->email ?? '';
            $this->customerPhone = $user->phone ?? '';
            $this->customerSearch = '';
        }
    }

    public function clearCustomerSelection(): void
    {
        $this->selectedCustomerId = null;
        $this->customerName = '';
        $this->customerEmail = '';
        $this->customerPhone = '';
        $this->customerSearch = '';
    }

    public function updatedSelectedProduct($skuId)
    {
        if ($skuId) {
            $sku = Sku::with('product')->find($skuId);
            if ($sku) {
                $this->price = (int) ($sku->selling_price ?? 0);
            }
        } else {
            $this->price = 0;
        }
    }

    public function addItem()
    {
        if (! $this->selectedProduct || $this->quantity < 1) {
            return;
        }

        $sku = Sku::with('product')->find($this->selectedProduct);
        if (! $sku) {
            return;
        }

        $availableStock = $sku->stock_quantity ?? 0;

        $existingQuantityInCart = 0;
        $existingKey = null;
        foreach ($this->items as $key => $item) {
            if ($item['sku_id'] == $this->selectedProduct) {
                $existingKey = $key;
                $existingQuantityInCart = $item['quantity'];
                break;
            }
        }

        $totalRequestedQuantity = $existingQuantityInCart + $this->quantity;

        if ($availableStock <= 0) {
            Notification::make()
                ->title('Stok Habis')
                ->body("Stok untuk \"{$sku->product->name} ({$sku->sku})\" habis.")
                ->danger()
                ->send();

            return;
        }

        if ($totalRequestedQuantity > $availableStock) {
            $message = $existingQuantityInCart > 0
                ? "Stok tidak mencukupi. Tersedia: {$availableStock}, di keranjang: {$existingQuantityInCart}, diminta: {$this->quantity}."
                : "Stok \"{$sku->product->name}\" Tersedia: {$availableStock}";

            Notification::make()
                ->title('Stok Tidak Mencukupi')
                ->body($message)
                ->warning()
                ->send();

            return;
        }

        if ($existingKey !== null) {
            $this->items[$existingKey]['quantity'] += $this->quantity;
            $this->items[$existingKey]['subtotal'] = $this->items[$existingKey]['quantity'] * $this->items[$existingKey]['price'];
        } else {
            $this->items[] = [
                'sku_id' => $sku->id,
                'product_id' => $sku->product_id,
                'product_name' => $sku->product->name,
                'sku_code' => $sku->sku,
                'price' => $this->price,
                'quantity' => $this->quantity,
                'subtotal' => $this->price * $this->quantity,
            ];
        }

        Notification::make()
            ->title('Item Ditambahkan')
            ->body("Berhasil menambahkan {$this->quantity}x \"{$sku->product->name}\" ke keranjang.")
            ->success()
            ->send();

        $this->reset(['selectedProduct', 'price', 'quantity']);
        $this->quantity = 1;
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function clearItems()
    {
        $this->items = [];
        $this->discount = 0;
    }

    public function getSubtotalProperty()
    {
        return collect($this->items)->sum('subtotal');
    }

    public function getGrandTotalProperty()
    {
        return max(0, $this->subtotal - (int) $this->discount);
    }

    public function checkout()
    {
        $this->validate();

        if (empty($this->items)) {
            session()->flash('error', 'Keranjang masih kosong');

            return;
        }

        DB::beginTransaction();
        try {
            $prefix = 'CSH-';
            do {
                $orderNumber = $prefix.strtoupper(substr(uniqid(), -6));
            } while (Order::where('order_number', $orderNumber)->exists());

            $order = Order::create([
                'order_number' => $orderNumber,
                'session_id' => (string) Str::uuid(),
                'user_id' => Auth::id(),
                'customer_name' => $this->customerName,
                'customer_email' => $this->customerEmail,
                'customer_phone' => $this->customerPhone,
                'subtotal' => $this->subtotal,
                'total' => $this->grandTotal,
                'discount_amount' => $this->discount,
                'shipping_province' => 'N/A',
                'shipping_city' => 'N/A',
                'shipping_district' => 'N/A',
                'shipping_postal_code' => 'N/A',
                'shipping_address' => 'N/A',
                'shipping_method' => 'offline',
                'shipping_service' => 'offline',
                'shipping_cost' => 0,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'status' => 'completed',
            ]);

            foreach ($this->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'sku_id' => $item['sku_id'],
                    'product_name' => $item['product_name'],
                    'sku_code' => $item['sku_code'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            DB::commit();

            $this->dispatch('showSuccessModal', orderNumber: $orderNumber);

            $this->reset(['items', 'customerName', 'customerEmail', 'customerPhone', 'discount', 'selectedCustomerId']);
            $this->quantity = 1;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function render()
    {
        $skus = Sku::with('product')
            ->whereHas('product', function ($query) {
                $query->where('is_active', true);
            })
            ->where('is_active', true)
            ->get();

        $products = $skus->mapWithKeys(fn ($sku) => [$sku->id => "{$sku->product->name} ({$sku->sku})"]);

        $productPrices = $skus->mapWithKeys(fn ($sku) => [$sku->id => (int) $sku->selling_price]);

        $recentTransactions = Order::where('payment_method', 'cash')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('livewire.pos', [
            'products' => $products,
            'productPrices' => $productPrices,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
