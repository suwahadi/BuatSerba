<?php

use App\Models\Order;
use App\Models\Payment;
use App\Models\PremiumMembership;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('midtrans.server_key', 'SB-Mid-server-test');
    config()->set('midtrans.is_production', false);
});

it('extracts qr_string and qr_url from a QRIS charge response', function () {
    $midtransResponse = [
        'status_code' => '201',
        'status_message' => 'QRIS transaction is created',
        'transaction_id' => 'tx-qris-1',
        'order_id' => 'TEST-QRIS-001',
        'gross_amount' => '100000.00',
        'payment_type' => 'qris',
        'transaction_status' => 'pending',
        'fraud_status' => 'accept',
        'qr_string' => '00020101021126610014COM.GO-JEK.WWW...',
        'actions' => [
            [
                'name' => 'generate-qr-code',
                'method' => 'GET',
                'url' => 'https://api.sandbox.midtrans.com/v2/qris/abc/qr-code',
            ],
        ],
        'expiry_time' => now()->addHours(24)->toIso8601String(),
    ];

    $instructions = (new MidtransService)->extractPaymentInstructions($midtransResponse);

    expect($instructions['type'])->toBe('qris');
    expect($instructions['qr_string'])->toBe('00020101021126610014COM.GO-JEK.WWW...');
    expect($instructions['qr_url'])->toBe('https://api.sandbox.midtrans.com/v2/qris/abc/qr-code');
});

it('preserves qr_string when Midtrans webhook notification arrives', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'order_number' => 'TEST-QRIS-002',
        'payment_method' => 'qris',
        'payment_status' => 'pending',
        'total' => 100000,
    ]);

    // 1) Charge response stored on the payment record (has qr_string + actions)
    $chargeResponse = [
        'status_code' => '201',
        'transaction_id' => 'tx-qris-2',
        'order_id' => 'TEST-QRIS-002',
        'gross_amount' => '100000.00',
        'payment_type' => 'qris',
        'transaction_status' => 'pending',
        'fraud_status' => 'accept',
        'qr_string' => '00020101021126610014COM.GO-JEK.WWW...PRESERVE-ME',
        'actions' => [
            [
                'name' => 'generate-qr-code',
                'method' => 'GET',
                'url' => 'https://api.sandbox.midtrans.com/v2/qris/xyz/qr-code',
            ],
        ],
        'expiry_time' => now()->addHours(24)->toIso8601String(),
    ];

    $payment = Payment::create([
        'order_id' => $order->id,
        'payment_gateway' => 'midtrans',
        'transaction_id' => 'tx-qris-2',
        'transaction_time' => now(),
        'transaction_status' => 'pending',
        'fraud_status' => 'accept',
        'payment_type' => 'qris',
        'payment_channel' => 'qris',
        'gross_amount' => 100000,
        'currency' => 'IDR',
        'midtrans_response' => $chargeResponse,
    ]);

    expect(data_get($payment->midtrans_response, 'qr_string'))->toBe('00020101021126610014COM.GO-JEK.WWW...PRESERVE-ME');

    // 2) Midtrans webhook notification — NO qr_string, NO actions
    $notification = [
        'transaction_status' => 'pending',
        'status_code' => '201',
        'signature_key' => 'fake-sig',
        'order_id' => 'TEST-QRIS-002',
        'gross_amount' => '100000.00',
        'payment_type' => 'qris',
        'transaction_id' => 'tx-qris-2',
        'fraud_status' => 'accept',
    ];

    $payment->updateFromMidtransNotification($notification);

    // 3) Critical assertion: qr_string MUST still be there post-merge.
    $payment->refresh();
    expect(data_get($payment->midtrans_response, 'qr_string'))
        ->toBe('00020101021126610014COM.GO-JEK.WWW...PRESERVE-ME')
        ->and(data_get($payment->midtrans_response, 'actions.0.url'))
        ->toBe('https://api.sandbox.midtrans.com/v2/qris/xyz/qr-code')
        ->and(data_get($payment->midtrans_response, 'signature_key'))
        ->toBe('fake-sig')  // notification fields still applied
        ->and($payment->transaction_status)
        ->toBe('pending');  // top-level columns also updated
});

it('preserves qr_string on PremiumMembership when webhook notification arrives', function () {
    $user = User::factory()->create();

    $membership = PremiumMembership::create([
        'user_id' => $user->id,
        'order_id' => 'PREM-QRIS-001',
        'amount' => 100000,
        'duration_days' => 365,
        'status' => 'pending',
        'transaction_status' => 'pending',
        'payment_method' => 'qris',
        'payment_channel' => 'qris',
        'started_at' => null,
        'expires_at' => null,
        'midtrans_response' => [
            'payment_type' => 'qris',
            'qr_string' => '00020101...PREMIUM-PRESERVE',
            'actions' => [
                ['name' => 'generate-qr-code', 'method' => 'GET', 'url' => 'https://api.sandbox.midtrans.com/v2/qris/prem/qr-code'],
            ],
        ],
    ]);

    $notification = [
        'transaction_status' => 'pending',
        'status_code' => '201',
        'signature_key' => 'sig',
        'order_id' => 'PREM-QRIS-001',
        'gross_amount' => '100000.00',
        'payment_type' => 'qris',
        'transaction_id' => 'tx-prem-1',
        'fraud_status' => 'accept',
    ];

    $membership->updateFromMidtransNotification($notification);

    $membership->refresh();
    expect(data_get($membership->midtrans_response, 'qr_string'))
        ->toBe('00020101...PREMIUM-PRESERVE')
        ->and(data_get($membership->midtrans_response, 'actions.0.url'))
        ->toBe('https://api.sandbox.midtrans.com/v2/qris/prem/qr-code');
});

it('survives a settlement notification without losing the qr_string', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'order_number' => 'TEST-QRIS-003',
        'payment_method' => 'qris',
        'payment_status' => 'pending',
        'total' => 50000,
    ]);

    $payment = Payment::create([
        'order_id' => $order->id,
        'payment_gateway' => 'midtrans',
        'transaction_id' => 'tx-qris-3',
        'transaction_time' => now(),
        'transaction_status' => 'pending',
        'payment_type' => 'qris',
        'gross_amount' => 50000,
        'midtrans_response' => [
            'payment_type' => 'qris',
            'qr_string' => 'INITIAL-QR-STRING',
            'actions' => [['name' => 'generate-qr-code', 'url' => 'https://example.test/qr']],
        ],
    ]);

    // First webhook: still pending
    $payment->updateFromMidtransNotification([
        'transaction_status' => 'pending',
        'status_code' => '201',
        'order_id' => 'TEST-QRIS-003',
        'payment_type' => 'qris',
    ]);

    // Second webhook: settlement
    $payment->updateFromMidtransNotification([
        'transaction_status' => 'settlement',
        'status_code' => '200',
        'order_id' => 'TEST-QRIS-003',
        'payment_type' => 'qris',
        'fraud_status' => 'accept',
    ]);

    $payment->refresh();
    expect(data_get($payment->midtrans_response, 'qr_string'))->toBe('INITIAL-QR-STRING');
    expect($payment->transaction_status)->toBe('settlement');
});
