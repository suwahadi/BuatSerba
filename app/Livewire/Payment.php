<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Payment as PaymentModel;
use App\Services\MidtransService;
use Livewire\Component;

class Payment extends Component
{
    public $order;

    public $paymentId;

    public $paymentInstructions = [];

    public $paymentData = [];

    protected $queryString = ['code'];

    public function mount($code = null)
    {
        if (! $code) {
            return redirect()->route('home');
        }

        // Load order by order number
        $this->order = Order::where('order_number', $code)->first();

        if (! $this->order) {
            session()->flash('error', 'Order tidak ditemukan.');

            return redirect()->route('home');
        }

        // Load payment if exists
        $payment = PaymentModel::where('order_id', $this->order->id)->first();

        if ($payment) {
            $this->paymentId = $payment->id;

            // If payment exists, load instructions from Core API response
            if ($payment->midtrans_response) {
                $midtransService = new MidtransService;
                $this->paymentInstructions = $midtransService->extractPaymentInstructions($payment->midtrans_response);

                // Store core API payment data
                $this->paymentData = [
                    'transaction_id' => $payment->transaction_id,
                    'transaction_status' => $payment->transaction_status,
                    'payment_type' => $payment->payment_type,
                    'payment_channel' => $payment->payment_channel,
                    'gross_amount' => $payment->gross_amount,
                ];
            }
        }
    }

    public function render()
    {
        return view('livewire.payment', [
            'order' => $this->order,
            'paymentInstructions' => $this->paymentInstructions,
            'paymentData' => $this->paymentData,
        ])->layout('components.layouts.guest');
    }
}
