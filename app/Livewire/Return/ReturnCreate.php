<?php

namespace App\Livewire\Return;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\ReturnRequestService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Component;
use Livewire\WithFileUploads;

class ReturnCreate extends Component
{
    use WithFileUploads;

    public string $selectedOrderId = '';

    public array $orders = [];

    public array $items = [];

    public string $selectedOrderItemId = '';

    public string $note = '';

    public array $photos = [];

    public array $uploadedPhotos = [];

    public bool $isLoading = false;

    public string $errorMessage = '';

    public bool $showConfirmDialog = false;

    public string $searchOrder = '';

    public function mount(?string $orderNumber = null): void
    {
        if (! Auth::check()) {
            redirect()->route('login');
        }

        $this->loadPaidOrders();

        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)
                ->where('user_id', Auth::id())
                ->where('payment_status', 'paid')
                ->where('status', 'completed')
                ->first();

            if ($order) {
                $this->selectedOrderId = (string) $order->id;
                $this->loadItemsForOrder($order);
            }
        }
    }

    public function loadPaidOrders(): void
    {
        $this->orders = Order::where('user_id', Auth::id())
            ->where('payment_status', 'paid')
            ->where('status', 'completed')
            ->whereDoesntHave('returnRequests')
            ->latest()
            ->get()
            ->map(function (Order $order) {
                return [
                    'id' => $order->id,
                    'label' => "{$order->order_number} - ".now()->parse($order->created_at)->format('d M Y'),
                ];
            })
            ->toArray();
    }

    public function updatedSelectedOrderId(): void
    {
        $this->errorMessage = '';
        $this->items = [];
        $this->selectedOrderItemId = '';

        if (blank($this->selectedOrderId)) {
            return;
        }

        $order = Order::find($this->selectedOrderId);

        if (! $order) {
            $this->errorMessage = 'Pesanan tidak ditemukan.';

            return;
        }

        $this->loadItemsForOrder($order);
    }

    private function loadItemsForOrder(Order $order): void
    {
        $this->items = $order->items()
            ->select('id', 'product_name', 'sku_code', 'quantity')
            ->get()
            ->map(function (OrderItem $item) {
                return [
                    'id' => (string) $item->id,
                    'label' => "{$item->product_name} - {$item->sku_code} (Qty: {$item->quantity})",
                ];
            })
            ->toArray();

        if (empty($this->items)) {
            $this->errorMessage = 'Pesanan ini tidak memiliki barang.';
        }
    }

    public function updatedPhotos(): void
    {
        $this->validate([
            'photos.*' => 'image|max:5120',
        ], [
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        if (count($this->uploadedPhotos) + count($this->photos) > 3) {
            $this->errorMessage = 'Maksimal 3 foto bukti yang dapat diunggah.';
            $this->photos = [];

            return;
        }

        foreach ($this->photos as $photo) {
            $this->uploadedPhotos[] = $photo;
        }

        $this->photos = [];
    }

    public function removePhoto(int $index): void
    {
        if (isset($this->uploadedPhotos[$index])) {
            unset($this->uploadedPhotos[$index]);
            $this->uploadedPhotos = array_values($this->uploadedPhotos);
        }
    }

    private function processAndSaveImages(): array
    {
        $savedPaths = [];

        foreach ($this->uploadedPhotos as $photo) {
            $filename = Str::random(20).'.webp';
            $image = Image::read($photo->getRealPath())
                ->scaleDown(1200, 1200)
                ->toWebp(85);
            $path = 'returns/'.date('Y/m').'/'.$filename;
            Storage::disk('public')->put($path, (string) $image);
            $savedPaths[] = $path;
        }

        return $savedPaths;
    }

    public function confirmSubmit(): void
    {
        $this->validate([
            'selectedOrderId' => 'required|string',
            'selectedOrderItemId' => 'required|string',
            'note' => 'nullable|string|max:1000',
            'uploadedPhotos' => 'nullable|array|max:3',
        ], [
            'selectedOrderId.required' => 'Pilih pesanan yang ingin diretur.',
            'selectedOrderItemId.required' => 'Pilih barang yang ingin diretur.',
            'note.max' => 'Catatan tidak boleh melebihi 1000 karakter.',
            'uploadedPhotos.max' => 'Maksimal 3 foto bukti.',
        ]);

        $this->showConfirmDialog = true;
    }

    public function cancelConfirm(): void
    {
        $this->showConfirmDialog = false;
    }

    public function submit(): void
    {
        $this->isLoading = true;
        $this->errorMessage = '';

        try {
            $order = Order::find($this->selectedOrderId);

            if (! $order) {
                throw new \InvalidArgumentException('Pesanan tidak ditemukan.');
            }

            $imagePaths = $this->processAndSaveImages();

            $service = new ReturnRequestService;
            $returnRequest = $service->createReturnRequest([
                'order_number' => $order->order_number,
                'order_item_id' => (int) $this->selectedOrderItemId,
                'note' => $this->note,
                'image_proof' => $imagePaths,
            ]);

            session()->flash('success', 'Permohonan retur berhasil dibuat. Menunggu persetujuan admin.');
            redirect()->route('returns.index');
        } catch (\InvalidArgumentException $e) {
            $this->errorMessage = $e->getMessage();
            $this->showConfirmDialog = false;
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan saat membuat permohonan retur.';
            \Illuminate\Support\Facades\Log::error('Return request creation failed', ['error' => $e->getMessage()]);
            $this->showConfirmDialog = false;
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.return-create')->layout('components.layouts.dashboard');
    }
}
