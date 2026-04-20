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

    public $qrCodeImage = null;

    protected $queryString = ['code'];

    public function mount($code = null)
    {
        if (! $code) {
            return redirect()->route('home');
        }

        $this->order = Order::where('order_number', $code)->first();

        if (! $this->order) {
            session()->flash('error', 'Order tidak ditemukan.');

            return redirect()->route('home');
        }

        $payment = PaymentModel::where('order_id', $this->order->id)->first();

        if ($payment) {
            $this->paymentId = $payment->id;

            if ($payment->midtrans_response) {
                $midtransService = new MidtransService;
                $this->paymentInstructions = $midtransService->extractPaymentInstructions($payment->midtrans_response);

                if (isset($this->paymentInstructions['type']) && $this->paymentInstructions['type'] === 'qris') {
                    $this->generateQrCode($this->paymentInstructions['qr_string'] ?? null);
                }

                $this->paymentData = [
                    'transaction_id' => $payment->transaction_id,
                    'transaction_status' => $payment->transaction_status,
                    'payment_type' => $payment->payment_type,
                    'payment_channel' => $payment->payment_channel,
                    'gross_amount' => $payment->gross_amount,
                    'expired_at' => $payment->expired_at?->toIso8601String(),
                ];
            }
        }
    }

    public function render()
    {
        // \Log::info('Payment page rendering', [
        //     'order_number' => $this->order->order_number,
        //     'payment_method' => $this->order->payment_method,
        //     'payment_status' => $this->order->payment_status,
        //     'order_status' => $this->order->status,
        //     'paymentInstructions' => $this->paymentInstructions,
        //     'paymentData' => $this->paymentData,
        // ]);
        
        return view('livewire.payment', [
            'order' => $this->order,
            'paymentInstructions' => $this->paymentInstructions,
            'paymentData' => $this->paymentData,
            'qrCodeImage' => $this->qrCodeImage,
        ])->layout('components.layouts.guest');
    }

    /**
     * Generate QR code image from QR string
     */
    public function generateQrCode(?string $qrString)
    {
        if (!$qrString) {
            $this->qrCodeImage = null;
            \Log::warning('Payment QR string is empty');
            return;
        }

        try {
            // \Log::info('Generating payment QR code', ['qr_string_length' => strlen($qrString)]);
            
            $options = new \chillerlan\QRCode\QROptions([
                'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
                'scale' => 8,
                'imageBase64' => true,
                'imageTransparent' => false,
            ]);
            
            $qrcode = new \chillerlan\QRCode\QRCode($options);
            $this->qrCodeImage = $qrcode->render($qrString);
            
            // \Log::info('Payment QR code generated successfully', [
            //     'image_length' => strlen($this->qrCodeImage)
            // ]);
        } catch (\Exception $e) {
            \Log::error('Payment QR code generation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->qrCodeImage = null;
        }
    }
}
