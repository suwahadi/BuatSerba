<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Payment as PaymentModel;
use App\Services\MidtransService;

class Payment extends Component
{
    public $order;
    public $paymentId;
    public $paymentInstructions = [];
    public $snapToken;
    public $showSnap = false;

    protected $queryString = ['code'];

    public function mount($code = null)
    {
        if (!$code) {
            return redirect()->route('home');
        }

        // Load order by order number
        $this->order = Order::where('order_number', $code)->first();
        
        if (!$this->order) {
            session()->flash('error', 'Order tidak ditemukan.');
            return redirect()->route('home');
        }

        // Load payment if exists
        $payment = PaymentModel::where('order_id', $this->order->id)->first();
        
        if ($payment) {
            $this->paymentId = $payment->id;
            
            // If payment exists, load instructions
            if ($payment->midtrans_response) {
                $midtransService = new MidtransService();
                $this->paymentInstructions = $midtransService->extractPaymentInstructions($payment->midtrans_response);
            }
            
            // Check if we need to show Snap payment
            if ($payment->snap_token) {
                $this->snapToken = $payment->snap_token;
                $this->showSnap = true;
            }
        }
    }

    public function render()
    {
        return view('livewire.payment', [
            'order' => $this->order,
            'paymentInstructions' => $this->paymentInstructions,
            'snapToken' => $this->snapToken,
            'showSnap' => $this->showSnap,
        ])->layout('components.layouts.guest');
    }
}