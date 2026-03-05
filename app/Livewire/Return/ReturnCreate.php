<?php

namespace App\Livewire\Return;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\ReturnRequestService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReturnCreate extends Component
{
    public string $selectedOrderId = '';

    public array $orders = [];

    public array $items = [];

    public string $selectedOrderItemId = '';

    public string $note = '';

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

    public function confirmSubmit(): void
    {
        $this->validate([
            'selectedOrderId' => 'required|string',
            'selectedOrderItemId' => 'required|string',
            'note' => 'nullable|string|max:1000',
        ], [
            'selectedOrderId.required' => 'Pilih pesanan yang ingin diretur.',
            'selectedOrderItemId.required' => 'Pilih barang yang ingin diretur.',
            'note.max' => 'Catatan tidak boleh melebihi 1000 karakter.',
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

            $service = new ReturnRequestService;
            $returnRequest = $service->createReturnRequest([
                'order_number' => $order->order_number,
                'order_item_id' => (int) $this->selectedOrderItemId,
                'note' => $this->note,
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
