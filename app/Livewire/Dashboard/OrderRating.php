<?php

namespace App\Livewire\Dashboard;

use App\Models\Order;
use App\Models\ProductReview;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.dashboard')]
#[Title('Beri Penilaian - BuatSerba')]
class OrderRating extends Component
{
    use WithFileUploads;

    public $orderNumber;

    public $items = []; // Flattened array of input data keyed by product_id

    public function mount($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        $order = $this->order;

        // Security checks
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'completed' && $order->status !== 'delivered') { // 'delivered' might be the DB status for 'Berhasil/Completed' in logic? Previous step used 'completed'. I'll check both or strict matches.
            // Previous step: case 'completed': $query->where('status', 'completed');
            // So status MUST be 'completed'.
            if ($order->status !== 'completed') {
                abort(403, 'Order belum selesai.');
            }
        }

        // Check if already reviewed
        if ($order->reviews()->exists()) {
            // Already reviewed, redirect or show error?
            // "Link user dashboard: /user/order/{order_number}/rating"
            // If already reviewed, maybe show the reviews?
            // For now, redirect to dashboard with message.
            return redirect()->route('dashboard')->with('error', 'Pesanan ini sudah dinilai.');
        }

        // Initialize state for each product
        // Initialize state for each product
        foreach ($order->items as $item) {
            $this->items[] = [
                'product_id' => $item->product_id,
                'rating' => 5,
                'review' => '',
                'images' => [],
                'temp_images' => [],
            ];
        }
    }

    public function updated($property, $value)
    {
        if (str_ends_with($property, '.temp_images')) {
            $parts = explode('.', $property);
            $index = $parts[1];

            if (! empty($value)) {
                $currentCount = count($this->items[$index]['images']);
                $newCount = count($value);

                if ($currentCount + $newCount > 5) {
                    $this->addError("items.{$index}.images", 'Maksimal 5 foto per produk.');
                    $this->items[$index]['temp_images'] = [];

                    return;
                }

                foreach ($value as $img) {
                    $this->items[$index]['images'][] = $img;
                }

                $this->items[$index]['temp_images'] = [];
            }
        }
    }

    public function getOrderProperty()
    {
        return Order::with(['items.product', 'reviews'])->where('order_number', $this->orderNumber)->firstOrFail();
    }

    public function removeImage($index, $imageIndex)
    {
        unset($this->items[$index]['images'][$imageIndex]);
        $this->items[$index]['images'] = array_values($this->items[$index]['images']);
    }

    public function save()
    {
        $this->validate([
            'items.*.rating' => 'required|integer|min:1|max:5',
            'items.*.review' => 'required|string|min:5',
            'items.*.images' => 'array|max:5',
            'items.*.images.*' => 'image|max:2048', // 2MB max
        ], [
            'items.*.rating.required' => 'Rating wajib diisi.',
            'items.*.review.required' => 'Ulasan wajib diisi.',
            'items.*.review.min' => 'Ulasan minimal 5 karakter.',
            'items.*.images.max' => 'Maksimal 5 foto per produk.',
            'items.*.images.*.image' => 'File harus berupa gambar.',
        ]);

        foreach ($this->items as $itemData) {
            $imagePaths = [];

            if (! empty($itemData['images'])) {
                foreach ($itemData['images'] as $image) {
                    $path = $image->store('reviews', 'public');
                    $imagePaths[] = $path;
                }
            }

            ProductReview::create([
                'product_id' => $itemData['product_id'],
                'user_id' => auth()->id(),
                'order_id' => $this->order->id,
                'rating' => $itemData['rating'],
                'review' => $itemData['review'],
                'images' => $imagePaths,
                'is_verified_purchase' => true,
                'is_approved' => true,
            ]);
        }

        session()->flash('success', 'Terima kasih atas penilaian Anda!');

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.dashboard.order-rating', [
            'order' => $this->order,
        ]);
    }
}
