<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_payment_record()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an order
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

        // Assert that the payment was created
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'transaction_id' => 'txn_123456',
            'transaction_status' => 'pending',
        ]);

        // Assert that the payment belongs to the order
        $this->assertTrue($payment->order->is($order));
    }

    /** @test */
    public function it_can_update_payment_status_from_midtrans_notification()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an order
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total' => 100000,
            'payment_method' => 'bank-transfer-bca',
            'payment_status' => 'pending',
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

        // Simulate a Midtrans notification for successful payment
        $notification = [
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'status_code' => '200',
            'status_message' => 'Success, transaction is found',
            'signature_key' => 'sample_signature_key',
        ];

        // Update payment from notification
        $payment->updateFromMidtransNotification($notification);

        // Refresh models
        $payment->refresh();
        $order->refresh();

        // Assert that the payment status was updated
        $this->assertEquals('settlement', $payment->transaction_status);
        $this->assertEquals('accept', $payment->fraud_status);

        // Assert that the order status was updated
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->status);
        $this->assertNotNull($order->paid_at);
    }
}
