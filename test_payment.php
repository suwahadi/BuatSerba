<?php

require_once 'vendor/autoload.php';

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a test user
$user = User::factory()->create();

// Create a test order
$order = Order::factory()->create([
    'user_id' => $user->id,
    'total' => 100000,
    'payment_method' => 'bank-transfer-bca',
]);

// Create a payment record
$payment = Payment::create([
    'order_id' => $order->id,
    'payment_gateway' => 'midtrans',
    'transaction_id' => 'txn_123456',
    'transaction_time' => now(),
    'transaction_status' => 'pending',
    'payment_type' => 'bank_transfer',
    'payment_channel' => 'bca',
    'gross_amount' => 100000,
    'currency' => 'IDR',
]);

echo "Payment created successfully!\n";
echo 'Payment ID: '.$payment->id."\n";
echo 'Order ID: '.$payment->order->id."\n";
echo 'Transaction Status: '.$payment->transaction_status."\n";

// Test updating payment from notification
$notification = [
    'transaction_status' => 'settlement',
    'fraud_status' => 'accept',
    'status_code' => '200',
    'status_message' => 'Success, transaction is found',
    'signature_key' => 'sample_signature_key',
];

$payment->updateFromMidtransNotification($notification);

// Refresh models
$payment->refresh();
$order->refresh();

echo "After notification update:\n";
echo 'Payment Status: '.$payment->transaction_status."\n";
echo 'Order Payment Status: '.$order->payment_status."\n";
echo 'Order Status: '.$order->status."\n";

echo "Test completed successfully!\n";
