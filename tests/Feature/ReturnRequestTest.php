<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Services\ReturnRequestService;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->order = Order::factory()->create([
        'user_id' => $this->user->id,
        'payment_status' => 'paid',
        'status' => 'completed',
    ]);
    $this->orderItem = OrderItem::factory()->create([
        'order_id' => $this->order->id,
    ]);
    $this->service = new ReturnRequestService;
});

it('user tidak bisa retur tanpa login', function () {
    $response = $this->get(route('returns.index'));

    $response->assertRedirect(route('login'));
});

it('user bisa melihat halaman retur list setelah login', function () {
    $response = $this->actingAs($this->user)->get(route('returns.index'));

    $response->assertSuccessful();
    $response->assertSeeLivewire('return.return-index');
});

it('user bisa melihat halaman retur create setelah login', function () {
    $response = $this->actingAs($this->user)->get(route('returns.create'));

    $response->assertSuccessful();
    $response->assertSeeLivewire('return.return-create');
});

it('user tidak bisa retur order_number milik user lain', function () {
    $this->actingAs($this->user);

    $otherUser = User::factory()->create();
    $otherOrder = Order::factory()->create([
        'user_id' => $otherUser->id,
        'payment_status' => 'paid',
        'status' => 'completed',
    ]);
    $otherOrderItem = OrderItem::factory()->create([
        'order_id' => $otherOrder->id,
    ]);

    expect(fn () => $this->service->createReturnRequest([
        'order_number' => $otherOrder->order_number,
        'order_item_id' => $otherOrderItem->id,
    ]))
        ->toThrow(InvalidArgumentException::class, 'Pesanan ini bukan milik Anda.');
});

it('user tidak bisa retur jika payment_status != paid', function () {
    $this->actingAs($this->user);

    $unpaidOrder = Order::factory()->create([
        'user_id' => $this->user->id,
        'payment_status' => 'pending',
        'status' => 'completed',
    ]);
    $unpaidOrderItem = OrderItem::factory()->create([
        'order_id' => $unpaidOrder->id,
    ]);

    expect(fn () => $this->service->createReturnRequest([
        'order_number' => $unpaidOrder->order_number,
        'order_item_id' => $unpaidOrderItem->id,
    ]))
        ->toThrow(InvalidArgumentException::class, 'Hanya pesanan yang sudah dibayar dapat diretur.');
});

it('user tidak bisa retur jika status != completed', function () {
    $this->actingAs($this->user);

    $processingOrder = Order::factory()->create([
        'user_id' => $this->user->id,
        'payment_status' => 'paid',
        'status' => 'processing',
    ]);
    $processingOrderItem = OrderItem::factory()->create([
        'order_id' => $processingOrder->id,
    ]);

    expect(fn () => $this->service->createReturnRequest([
        'order_number' => $processingOrder->order_number,
        'order_item_id' => $processingOrderItem->id,
    ]))
        ->toThrow(InvalidArgumentException::class, 'Pesanan harus sudah selesai untuk dapat diretur.');
});

it('user tidak bisa retur order_item_id yang bukan milik order itu', function () {
    $this->actingAs($this->user);

    $otherOrder = Order::factory()->create([
        'user_id' => $this->user->id,
        'payment_status' => 'paid',
        'status' => 'completed',
    ]);
    $otherOrderItem = OrderItem::factory()->create([
        'order_id' => $otherOrder->id,
    ]);

    expect(fn () => $this->service->createReturnRequest([
        'order_number' => $this->order->order_number,
        'order_item_id' => $otherOrderItem->id,
    ]))
        ->toThrow(InvalidArgumentException::class, 'Item pembelian tidak ditemukan atau tidak sesuai dengan pesanan.');
});

it('user tidak bisa retur order_item yang sama 2x (unique order_item_id)', function () {
    $this->actingAs($this->user);

    $returnRequest = $this->service->createReturnRequest([
        'order_number' => $this->order->order_number,
        'order_item_id' => $this->orderItem->id,
        'note' => 'Barang rusak',
    ]);

    expect($returnRequest)->toBeInstanceOf(ReturnRequest::class);
    expect($returnRequest->status->value)->toBe('pending');

    expect(fn () => $this->service->createReturnRequest([
        'order_number' => $this->order->order_number,
        'order_item_id' => $this->orderItem->id,
    ]))
        ->toThrow(InvalidArgumentException::class, 'Item ini sudah pernah diajukan retur.');
});

it('service dapat membuat return request dengan benar', function () {
    $this->actingAs($this->user);

    $returnRequest = $this->service->createReturnRequest([
        'order_number' => $this->order->order_number,
        'order_item_id' => $this->orderItem->id,
        'note' => 'Barang rusak saat dikirim',
    ]);

    expect($returnRequest)->toBeInstanceOf(ReturnRequest::class);
    expect($returnRequest->order_id)->toBe($this->order->id);
    expect($returnRequest->user_id)->toBe($this->user->id);
    expect($returnRequest->order_number)->toBe($this->order->order_number);
    expect($returnRequest->status->value)->toBe('pending');
    expect($returnRequest->note)->toBe('Barang rusak saat dikirim');
    expect($returnRequest->items()->count())->toBe(1);
});

it('admin dapat approve return request dengan pending status', function () {
    $this->actingAs($this->user);
    $returnRequest = $this->service->createReturnRequest([
        'order_number' => $this->order->order_number,
        'order_item_id' => $this->orderItem->id,
    ]);

    $admin = User::factory()->create(['role' => 'admin']);

    $returnRequest->update([
        'status' => 'approved',
        'handled_by' => $admin->id,
        'handled_at' => now(),
    ]);

    $returnRequest->refresh();

    expect($returnRequest->status->value)->toBe('approved');
    expect($returnRequest->handled_by)->toBe($admin->id);
    expect($returnRequest->handled_at)->not->toBeNull();
});

it('admin dapat reject return request dengan alasan', function () {
    $this->actingAs($this->user);
    $returnRequest = $this->service->createReturnRequest([
        'order_number' => $this->order->order_number,
        'order_item_id' => $this->orderItem->id,
    ]);

    $admin = User::factory()->create(['role' => 'admin']);

    $returnRequest->update([
        'status' => 'rejected',
        'admin_note' => 'Tidak memenuhi kriteria retur',
        'handled_by' => $admin->id,
        'handled_at' => now(),
    ]);

    $returnRequest->refresh();

    expect($returnRequest->status->value)->toBe('rejected');
    expect($returnRequest->admin_note)->toBe('Tidak memenuhi kriteria retur');
    expect($returnRequest->handled_by)->toBe($admin->id);
});

it('return request menggunakan database transaction', function () {
    $this->actingAs($this->user);

    $returnRequest = $this->service->createReturnRequest([
        'order_number' => $this->order->order_number,
        'order_item_id' => $this->orderItem->id,
    ]);

    expect(ReturnRequest::find($returnRequest->id))->not->toBeNull();
    expect($returnRequest->items()->count())->toBe(1);
});

it('order dengan return request tidak muncul di list dropdown pesanan', function () {
    $this->actingAs($this->user);

    $returnRequest = $this->service->createReturnRequest([
        'order_number' => $this->order->order_number,
        'order_item_id' => $this->orderItem->id,
    ]);

    $availableOrders = Order::where('user_id', $this->user->id)
        ->where('payment_status', 'paid')
        ->where('status', 'completed')
        ->whereDoesntHave('returnRequests')
        ->count();

    expect($availableOrders)->toBe(0);
});
