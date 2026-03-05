<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ReturnRequestService
{
    /**
     * Validate and create return request
     *
     * @param  array<string, mixed>  $data
     *
     * @throws InvalidArgumentException
     */
    public function createReturnRequest(array $data): ReturnRequest
    {
        return DB::transaction(function () use ($data) {
            $order = $this->validateAndGetOrder($data['order_number']);
            $orderItem = $this->validateAndGetOrderItem($order, $data['order_item_id']);

            $returnRequest = ReturnRequest::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'order_number' => $order->order_number,
                'status' => 'pending',
                'note' => $data['note'] ?? null,
            ]);

            ReturnRequestItem::create([
                'return_request_id' => $returnRequest->id,
                'order_item_id' => $orderItem->id,
                'product_id' => $orderItem->product_id,
                'sku_id' => $orderItem->sku_id,
                'quantity' => $orderItem->quantity,
            ]);

            return $returnRequest->load('items');
        });
    }

    /**
     * Validate order and check if it belongs to current user
     *
     * @throws InvalidArgumentException
     */
    private function validateAndGetOrder(string $orderNumber): Order
    {
        $order = Order::where('order_number', $orderNumber)->first();

        if (! $order) {
            throw new InvalidArgumentException('Nomor pesanan tidak ditemukan.');
        }

        if ($order->user_id !== Auth::id()) {
            throw new InvalidArgumentException('Pesanan ini bukan milik Anda.');
        }

        if ($order->payment_status !== 'paid') {
            throw new InvalidArgumentException('Hanya pesanan yang sudah dibayar dapat diretur.');
        }

        if ($order->status !== 'completed') {
            throw new InvalidArgumentException('Pesanan harus sudah selesai untuk dapat diretur.');
        }

        return $order;
    }

    /**
     * Validate order item and check if it belongs to the order
     *
     * @throws InvalidArgumentException
     */
    private function validateAndGetOrderItem(Order $order, int $orderItemId): OrderItem
    {
        $orderItem = OrderItem::where('id', $orderItemId)
            ->where('order_id', $order->id)
            ->first();

        if (! $orderItem) {
            throw new InvalidArgumentException('Item pembelian tidak ditemukan atau tidak sesuai dengan pesanan.');
        }

        $existingReturn = ReturnRequestItem::where('order_item_id', $orderItemId)->exists();
        if ($existingReturn) {
            throw new InvalidArgumentException('Item ini sudah pernah diajukan retur.');
        }

        return $orderItem;
    }
}
