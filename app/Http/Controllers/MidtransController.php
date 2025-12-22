<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment as PaymentModel;
use App\Models\PaymentNotification;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    /**
     * Handle Midtrans notification callback
     * This is the ONLY place where payment status is updated from Midtrans notifications
     */
    public function notification(Request $request)
    {
        try {
            $notif = $request->all();
            $orderId = $notif['order_id'] ?? null;
            $transactionStatus = $notif['transaction_status'] ?? null;

            // Validate Midtrans signature for security
            if (! $this->isValidSignature($notif)) {
                \Log::warning('Invalid Midtrans signature', ['order_id' => $orderId]);

                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
            }

            if (! $orderId) {
                \Log::warning('Missing order_id in Midtrans notification');

                return response()->json(['status' => 'error', 'message' => 'Missing order_id'], 400);
            }

            // Handle Midtrans test notification
            if (strpos($orderId, 'payment_notif_test_') === 0) {
                \Log::info('Test notification received', ['order_id' => $orderId]);

                return response()->json([
                    'status' => 'ok',
                    'message' => 'Test notification handled successfully',
                ]);
            }

            // Find order by order number
            $order = Order::where('order_number', $orderId)->first();
            if (! $order) {
                \Log::warning('Order not found for Midtrans notification', ['order_id' => $orderId]);

                return response()->json([
                    'status' => 'ok',
                    'message' => 'Order not found but notification acknowledged',
                ], 200);
            }

            // Find or create payment record
            $payment = PaymentModel::where('order_id', $order->id)->first();

            // Create payment notification audit record
            $paymentNotification = PaymentNotification::create([
                'payment_id' => $payment->id ?? null,
                'order_id' => $order->order_number,
                'transaction_status' => $transactionStatus,
                'notification_body' => $notif,
                'processed' => false,
            ]);

            \Log::info('Payment notification created', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_id' => $payment->id ?? null,
            ]);

            // Update payment and order status through standardized payment update
            if ($payment) {
                $payment->updateFromMidtransNotification($notif);
                \Log::info('Payment updated from notification', ['payment_id' => $payment->id]);
            } else {
                // If no payment exists, update order directly
                $order->updatePaymentStatus($transactionStatus, $notif['fraud_status'] ?? null, $notif);
                \Log::info('Order updated from notification', ['order_id' => $order->id]);
            }

            // Refresh order to get latest status
            $order->refresh();

            // Send payment success email if payment is confirmed
            if ($order->payment_status === 'paid') {
                \App\Jobs\SendPaymentSuccessEmail::dispatch($order);
                \Log::info('Payment success email job dispatched', ['order_number' => $order->order_number]);
            }

            // Mark notification as processed
            $paymentNotification->update(['processed' => true, 'processed_at' => now()]);

            // Always return OK to Midtrans (we processed it successfully)
            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            \Log::error('Midtrans notification processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Still return OK to Midtrans to prevent retry loops for invalid data
            return response()->json([
                'status' => 'ok',
                'message' => 'Notification processed with error logged',
            ], 200);
        }
    }

    /**
     * Handle successful payment redirect from Midtrans
     */
    public function finish(Request $request)
    {
        $orderId = $request->query('order_id');

        if ($orderId) {
            $order = Order::where('order_number', $orderId)->first();
            if ($order) {
                return redirect()->route('payment.success', ['orderNumber' => $order->order_number]);
            }
        }

        return redirect()->route('home')->with('error', 'Order not found');
    }

    /**
     * Handle unfinished payment redirect from Midtrans
     */
    public function unfinish(Request $request)
    {
        $orderId = $request->query('order_id');

        if ($orderId) {
            $order = Order::where('order_number', $orderId)->first();
            if ($order) {
                return redirect()->route('payment.show', ['orderNumber' => $order->order_number]);
            }
        }

        return redirect()->route('home')->with('error', 'Order not found');
    }

    /**
     * Handle error payment redirect from Midtrans
     */
    public function error(Request $request)
    {
        $orderId = $request->query('order_id');

        if ($orderId) {
            $order = Order::where('order_number', $orderId)->first();
            if ($order) {
                return redirect()->route('payment.failed', ['orderNumber' => $order->order_number]);
            }
        }

        return redirect()->route('home')->with('error', 'Order not found');
    }

    /**
     * Validate Midtrans notification signature for security
     */
    private function isValidSignature(array $notif): bool
    {
        $serverKey = config('midtrans.server_key');
        if (! $serverKey) {
            \Log::warning('Midtrans server_key not configured');

            return false;
        }

        $orderId = $notif['order_id'] ?? '';
        $statusCode = $notif['status_code'] ?? '';
        $grossAmount = $notif['gross_amount'] ?? '';

        $signature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);
        $providedSignature = $notif['signature_key'] ?? '';

        return hash_equals($signature, $providedSignature);
    }
}
