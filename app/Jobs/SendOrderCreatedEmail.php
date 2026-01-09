<?php

namespace App\Jobs;

use App\Mail\OrderCreated;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderCreatedEmail implements ShouldQueue
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
                ->send(new OrderCreated($this->order));

            Log::info('Order confirmation email sent', [
                'order_number' => $this->order->order_number,
                'email' => $this->order->customer_email,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation email', [
                'order_number' => $this->order->order_number,
                'email' => $this->order->customer_email,
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
        Log::error('Order confirmation email failed permanently', [
            'order_number' => $this->order->order_number,
            'email' => $this->order->customer_email,
            'error' => $exception->getMessage(),
        ]);
    }
}
