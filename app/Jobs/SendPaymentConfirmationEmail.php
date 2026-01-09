<?php

namespace App\Jobs;

use App\Mail\PaymentConfirmationAdmin;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendPaymentConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order,
        public array $data,
        public ?string $proofPath = null
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adminEmail = global_config('admin_email');

        if ($adminEmail) {
            Mail::to($adminEmail)->send(new PaymentConfirmationAdmin($this->order, $this->data, $this->proofPath));
        } else {
             // Fallback or log warning if admin email not configured
             Log::warning("Admin email not found in global config. Cannot send payment confirmation email for order {$this->order->order_number}.");
        }
    }
}
