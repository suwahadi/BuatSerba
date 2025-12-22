<?php

namespace App\Jobs;

use App\Mail\PaymentSuccessAdmin;
use App\Mail\PaymentSuccessCustomer;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentSuccessEmail implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Send email to customer
            Mail::to($this->order->customer_email)
                ->send(new PaymentSuccessCustomer($this->order));

            Log::info('Payment success email sent to customer', [
                'order_number' => $this->order->order_number,
                'customer_email' => $this->order->customer_email,
            ]);

            // Send email to admin
            $adminEmail = config('mail.admin_email');
            if ($adminEmail) {
                Mail::to($adminEmail)
                    ->send(new PaymentSuccessAdmin($this->order));

                Log::info('Payment success email sent to admin', [
                    'order_number' => $this->order->order_number,
                    'admin_email' => $adminEmail,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send payment success email', [
                'order_number' => $this->order->order_number,
                'error' => $e->getMessage(),
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Payment success email failed permanently', [
            'order_number' => $this->order->order_number,
            'error' => $exception->getMessage(),
        ]);
    }
}
