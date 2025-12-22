<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\Product;
use App\Models\Sku;
use Livewire\Component;

class ProductDetail extends Component
{
    public $product;

    public $selectedSku;

    public $quantity = 1;

    public $selectedVariants = [];

    public $activeTab = 'description';

    public function mount($slug)
    {
        $this->product = Product::with(['category', 'skus' => function ($query) {
            $query->where('is_active', true);
        }])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Select first available SKU by default
        $this->selectedSku = $this->product->skus->first();

        // Initialize selected variants based on first SKU
        if ($this->selectedSku) {
            $this->initializeVariants();
        }

        // Increment view count
        $this->product->increment('view_count');
    }

    protected function initializeVariants()
    {
        $attributes = $this->selectedSku->attributes ?? [];

        foreach ($attributes as $key => $value) {
            $this->selectedVariants[$key] = $value;
        }
    }

    public function selectVariant($attributeName, $value)
    {
        $this->selectedVariants[$attributeName] = $value;
        $this->updateSelectedSku();
    }

    protected function updateSelectedSku()
    {
        // Find SKU that matches selected variants
        foreach ($this->product->skus as $sku) {
            $skuAttributes = $sku->attributes ?? [];
            $matches = true;

            foreach ($this->selectedVariants as $key => $value) {
                if (! isset($skuAttributes[$key]) || $skuAttributes[$key] != $value) {
                    $matches = false;
                    break;
                }
            }

            if ($matches) {
                $this->selectedSku = $sku;

                return;
            }
        }
    }

    public function incrementQuantity()
    {
        if ($this->quantity < $this->selectedSku->stock_quantity) {
            $this->quantity++;
        }
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function updatedQuantity($value)
    {
        if ($value < 1) {
            $this->quantity = 1;
        } elseif ($value > $this->selectedSku->stock_quantity) {
            $this->quantity = $this->selectedSku->stock_quantity;
        }
    }

    public function buyNow()
    {
        $this->addToCart();

        if (! session()->has('error')) {
            return redirect()->route('cart');
        }
    }

    public function addTierToCart($quantity)
    {
        // Set the quantity based on the tier clicked
        $this->quantity = $quantity;

        // Add to cart
        $this->addToCart();

        // Redirect to cart if no errors
        if (! session()->has('error')) {
            return redirect()->route('cart');
        }
    }

    public function addToCart()
    {
        if (! $this->selectedSku) {
            session()->flash('error', 'Silakan pilih variant produk terlebih dahulu.');

            return;
        }

        if ($this->selectedSku->stock_quantity < $this->quantity) {
            session()->flash('error', 'Stok tidak mencukupi.');

            return;
        }

        // Get or create session ID for cart
        if (! session()->has('cart_session_id')) {
            session()->put('cart_session_id', session()->getId());
        }

        $sessionId = session()->get('cart_session_id');

        // Check if item already exists in cart
        $existingItem = \App\Models\CartItem::where('sku_id', $this->selectedSku->id)
            ->where(function ($query) use ($sessionId) {
                $query->where('session_id', $sessionId);
                if (auth()->check()) {
                    $query->orWhere('user_id', auth()->id());
                }
            })
            ->first();

        if ($existingItem) {
            // Update quantity if item exists
            $newQuantity = $existingItem->quantity + $this->quantity;

            if ($newQuantity > $this->selectedSku->stock_quantity) {
                session()->flash('error', 'Jumlah melebihi stok yang tersedia.');

                return;
            }

            // Update quantity and price
            $existingItem->quantity = $newQuantity;
            $existingItem->price = $this->selectedSku->getPriceForQuantity($newQuantity);
            $existingItem->save();

            // Remove the flash message that was interfering with popup
            // session()->flash('message', 'Jumlah produk di keranjang berhasil diupdate!');
        } else {
            // Create new cart item
            \App\Models\CartItem::create([
                'session_id' => $sessionId,
                'user_id' => auth()->id(),
                'product_id' => $this->product->id,
                'sku_id' => $this->selectedSku->id,
                'quantity' => $this->quantity,
                'price' => $this->selectedSku->getPriceForQuantity($this->quantity),
            ]);

            // Remove the flash message that was interfering with popup
            // session()->flash('message', 'Produk berhasil ditambahkan ke keranjang!');
        }

        // Dispatch event for showing popup notification
        $this->dispatch('cartUpdated');
        $this->dispatch('show-cart-notification', [
            'productName' => $this->product->name,
            'quantity' => $this->quantity,
            'price' => $this->selectedSku->getPriceForQuantity($this->quantity),
        ]);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function getRelatedProductsProperty()
    {
        return Product::with(['category', 'skus'])
            ->where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();
    }

    public function getAvailableVariantsProperty()
    {
        $variants = [];

        foreach ($this->product->skus as $sku) {
            $attributes = $sku->attributes ?? [];

            foreach ($attributes as $key => $value) {
                if (! isset($variants[$key])) {
                    $variants[$key] = [];
                }

                if (! in_array($value, $variants[$key])) {
                    $variants[$key][] = $value;
                }
            }
        }

        return $variants;
    }

    // Get branch inventory information for the selected SKU
    public function getBranchInventoryProperty()
    {
        if (! $this->selectedSku) {
            return collect();
        }

        return BranchInventory::with('branch')
            ->where('sku_id', $this->selectedSku->id)
            ->where('quantity_available', '>', 0)
            ->join('branches', 'branch_inventory.branch_id', '=', 'branches.id')
            ->orderBy('branches.priority')
            ->select('branch_inventory.*')
            ->get();
    }

    public function render()
    {
        return view('livewire.product-detail', [
            'relatedProducts' => $this->relatedProducts,
            'availableVariants' => $this->availableVariants,
            'branchInventory' => $this->branchInventory,
        ])->layout('components.layouts.guest');
    }
}
