<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLog;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    /**
     * Handle Midtrans notification callback
     */
    public function notification(Request $request)
    {
        try {
            $notif = $request->all();
            $orderId = $notif['order_id'] ?? null;
            $transactionStatus = $notif['transaction_status'] ?? null;

            // Validate Midtrans signature for security
            if (! $this->isValidSignature($notif)) {
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
            }

            if (! $orderId) {
                return response()->json(['status' => 'error', 'message' => 'Missing order_id'], 400);
            }

            // Handle Midtrans test notification
            if (strpos($orderId, 'payment_notif_test_') === 0 || strpos($orderId, 'test_') === 0) {
                return response()->json([
                    'status' => 'ok',
                    'message' => 'Test notification handled successfully',
                ]);
            }

            $order = Order::where('order_number', $orderId)->first();
            if (! $order) {
                return response()->json([
                    'status' => 'ok',
                    'message' => 'Order not found but notification acknowledged',
                ], 200);
            }

            $payment = Payment::where('order_id', $order->id)->first();

            // Log the payment notification (only if payment record exists)
            if ($payment) {
                PaymentLog::create([
                    'payment_id' => $payment->id,
                    'event_type' => 'midtrans_notification',
                    'request_data' => $notif,
                    'response_data' => null,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            if ($payment) {
                $payment->updateFromMidtransNotification($notif);
            } else {
                $order->updatePaymentStatus($transactionStatus, $notif['fraud_status'] ?? null, $notif);
            }

            // For any status update, store callback info for frontend polling
            $callbackData = [
                'order_number' => $order->order_number,
                'payment_status' => in_array($transactionStatus, ['settlement', 'capture']) &&
                    ($notif['fraud_status'] ?? 'accept') === 'accept' ? 'paid' : $transactionStatus,
                'transaction_status' => $transactionStatus,
                'should_redirect' => in_array($transactionStatus, ['settlement', 'capture']) &&
                    ($notif['fraud_status'] ?? 'accept') === 'accept',
                'timestamp' => now()->toISOString(),
                'callback_data' => $notif,
            ];

            // Store in session
            session()->put("payment_callback_{$order->order_number}", $callbackData);

            // Also store in cache for additional reliability (5 minutes)
            cache()->put("payment_callback_{$order->order_number}", $callbackData, 300);

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
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
        // Skip validation for test notifications
        if (isset($notif['order_id']) && strpos($notif['order_id'], 'payment_notif_test_') === 0) {
            return true;
        }

        $serverKey = config('midtrans.server_key');
        if (! $serverKey) {
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
