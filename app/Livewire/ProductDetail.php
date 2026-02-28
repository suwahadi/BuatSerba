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

    public $currentCarouselIndex = 0;

    public function mount($slug)
    {
        $this->product = Product::with(['category', 'reviews' => function ($query) {
            $query->where('is_approved', true);
        }, 'reviews.user', 'skus' => function ($query) {
            $query->where('is_active', true);
        }])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $skuId = request()->query('sku');
        $this->selectedSku = null;

        if ($skuId) {
            $this->selectedSku = $this->product->skus()->where('is_active', true)->where('id', (int) $skuId)->first();
        }

        if (! $this->selectedSku) {
            $this->selectedSku = $this->product->skus()->where('is_active', true)->first();
        }

        if ($this->selectedSku) {
            $this->initializeVariants();
            
            // Set initial carousel index for selected SKU
            $variantImageIndex = $this->variantImageIndex;
            if (isset($variantImageIndex[$this->selectedSku->id])) {
                $this->currentCarouselIndex = $variantImageIndex[$this->selectedSku->id];
            }
        }

        $this->product->increment('view_count');
    }

    protected function initializeVariants()
    {
        $attributes = $this->selectedSku->attributes ?? [];

        foreach ($attributes as $key => $value) {
            if ($key === 'image') {
                continue;
            }

            if ($value === null) {
                continue;
            }

            if (is_string($value) && trim($value) === '') {
                continue;
            }

            $this->selectedVariants[$key] = $value;
        }
    }

    public function selectVariant($attributeName, $value)
    {
        if ($attributeName === 'image') {
            return;
        }

        $this->selectedVariants[$attributeName] = $value;
        $this->updateSelectedSku();

        // Update carousel index when SKU has an image
        if ($this->selectedSku) {
            $variantImageIndex = $this->variantImageIndex;
            if (isset($variantImageIndex[$this->selectedSku->id])) {
                $this->currentCarouselIndex = $variantImageIndex[$this->selectedSku->id];
            }
        }
    }

    protected function updateSelectedSku()
    {
        $relevantSelected = array_filter($this->selectedVariants, function ($v) {
            if ($v === null) return false;
            if (is_string($v) && trim($v) === '') return false;
            return true;
        });

        if (! empty($relevantSelected)) {
            foreach ($this->product->skus()->where('is_active', true)->get() as $sku) {
                $skuAttributes = $sku->attributes ?? [];
                $matches = true;

                foreach ($relevantSelected as $key => $value) {
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

        if (! empty($relevantSelected)) {
            foreach ($this->product->skus()->where('is_active', true)->get() as $sku) {
                $skuAttributes = $sku->attributes ?? [];

                foreach ($relevantSelected as $value) {
                    if (in_array($value, $skuAttributes, true)) {
                        $this->selectedSku = $sku;

                        return;
                    }
                }
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
        $this->quantity = $quantity;

        $this->addToCart();

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

        if (! session()->has('cart_session_id')) {
            session()->put('cart_session_id', session()->getId());
        }

        $sessionId = session()->get('cart_session_id');

        $existingItem = \App\Models\CartItem::where('sku_id', $this->selectedSku->id)
            ->where(function ($query) use ($sessionId) {
                $query->where('session_id', $sessionId);
                if (auth()->check()) {
                    $query->orWhere('user_id', auth()->id());
                }
            })
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $this->quantity;

            if ($newQuantity > $this->selectedSku->stock_quantity) {
                session()->flash('error', 'Jumlah melebihi stok yang tersedia.');

                return;
            }

            $existingItem->quantity = $newQuantity;
            $existingItem->price = $this->selectedSku->getPriceForQuantity($newQuantity);
            $existingItem->save();
        } else {
            \App\Models\CartItem::create([
                'session_id' => $sessionId,
                'user_id' => auth()->id(),
                'product_id' => $this->product->id,
                'sku_id' => $this->selectedSku->id,
                'quantity' => $this->quantity,
                'price' => $this->selectedSku->getPriceForQuantity($this->quantity),
            ]);
        }

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
        foreach ($this->product->skus()->where('is_active', true)->get() as $sku) {
            $attributes = $sku->attributes ?? [];

            foreach ($attributes as $key => $value) {
                if ($key === 'image') {
                    continue;
                }

                if ($value === null) {
                    continue;
                }

                if (is_string($value) && trim($value) === '') {
                    continue;
                }

                if (! isset($variants[$key])) {
                    $variants[$key] = [];
                }

                if (! in_array($value, $variants[$key], true)) {
                    $variants[$key][] = $value;
                }
            }
        }

        return $variants;
    }

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

    public function getAverageRatingProperty()
    {
        return $this->product->reviews->avg('rating') ?? 0;
    }

    public function getReviewCountProperty()
    {
        return $this->product->reviews->count();
    }

    public function getCarouselImagesProperty()
    {
        $images = [];
        
        // Start with main image
        if ($this->product->main_image) {
            $images[] = $this->product->main_image;
        }
        
        // Add variant images from SKUs
        foreach ($this->product->skus()->where('is_active', true)->get() as $sku) {
            $attributes = $sku->attributes ?? [];
            
            if (isset($attributes['image']) && !empty($attributes['image'])) {
                // Avoid duplicates
                if (!in_array($attributes['image'], $images)) {
                    $images[] = $attributes['image'];
                }
            }
        }
        
        return $images;
    }

    public function getVariantImageIndexProperty()
    {
        $mapping = [];
        $images = $this->carouselImages;
        
        // Map SKU ID to carousel image index
        foreach ($this->product->skus()->where('is_active', true)->get() as $sku) {
            $attributes = $sku->attributes ?? [];
            
            if (isset($attributes['image']) && !empty($attributes['image'])) {
                $index = array_search($attributes['image'], $images);
                if ($index !== false) {
                    $mapping[$sku->id] = $index;
                }
            } else {
                // If SKU doesn't have specific image, fallback to main image (index 0)
                $mapping[$sku->id] = 0;
            }
        }
        
        return $mapping;
    }

    public function render()
    {
        return view('livewire.product-detail', [
            'relatedProducts' => $this->relatedProducts,
            'availableVariants' => $this->availableVariants,
            'branchInventory' => $this->branchInventory,
            'carouselImages' => $this->carouselImages,
            'variantImageIndex' => $this->variantImageIndex,
        ])->layout('components.layouts.guest');
    }
}
