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

                // Last-resort fallback: if QRIS but qr_string AND qr_url both missing
                // (e.g. legacy payments hit by the old overwrite bug), try to re-fetch
                // from Midtrans /status to rebuild the response, then re-extract.
                if (
                    ($this->paymentInstructions['type'] ?? null) === 'qris'
                    && empty($this->paymentInstructions['qr_string'])
                    && empty($this->paymentInstructions['qr_url'])
                    && $this->order->payment_status === 'pending'
                ) {
                    \Log::warning('QRIS payment missing qr_string/qr_url, attempting Midtrans re-fetch', [
                        'order_number' => $this->order->order_number,
                        'payment_id' => $payment->id,
                    ]);
                    $statusResult = $midtransService->getTransactionStatus($this->order->order_number);
                    if (($statusResult['success'] ?? false) && is_array($statusResult['data'] ?? null)) {
                        $merged = array_merge(
                            is_array($payment->midtrans_response) ? $payment->midtrans_response : [],
                            $statusResult['data']
                        );
                        $payment->update(['midtrans_response' => $merged]);
                        $this->paymentInstructions = $midtransService->extractPaymentInstructions($merged);
                        \Log::info('QRIS payment re-fetch result', [
                            'has_qr_string' => !empty($this->paymentInstructions['qr_string']),
                            'has_qr_url' => !empty($this->paymentInstructions['qr_url']),
                        ]);
                    }
                }

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
        \Log::info('Payment page rendering', [
            'order_number' => $this->order->order_number,
            'payment_method' => $this->order->payment_method,
            'payment_status' => $this->order->payment_status,
            'order_status' => $this->order->status,
            'payment_instructions_type' => $this->paymentInstructions['type'] ?? null,
            'payment_instructions_has_qr_string' => !empty($this->paymentInstructions['qr_string']),
            'payment_instructions_has_qr_url' => !empty($this->paymentInstructions['qr_url']),
            'qr_code_image_present' => !empty($this->qrCodeImage),
        ]);

        return view('livewire.payment', [
            'order' => $this->order,
            'paymentInstructions' => $this->paymentInstructions,
            'paymentData' => $this->paymentData,
            'qrCodeImage' => $this->qrCodeImage,
        ])->layout('components.layouts.guest');
    }

    /**
     * Re-fetch order + payment from DB so wire:poll picks up webhook-driven
     * status changes without forcing the user to refresh the page manually.
     */
    public function pollStatus()
    {
        if (!$this->order) {
            return;
        }

        $this->order->refresh();

        if ($this->paymentId) {
            $payment = PaymentModel::find($this->paymentId);
            if ($payment) {
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
            \Log::info('Generating payment QR code', ['qr_string_length' => strlen($qrString)]);

            $options = new \chillerlan\QRCode\QROptions([
                'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
                'scale' => 8,
                'imageBase64' => true,
                'imageTransparent' => false,
            ]);

            $qrcode = new \chillerlan\QRCode\QRCode($options);
            $this->qrCodeImage = $qrcode->render($qrString);

            \Log::info('Payment QR code generated successfully', [
                'image_length' => strlen($this->qrCodeImage)
            ]);
        } catch (\Exception $e) {
            \Log::error('Payment QR code generation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->qrCodeImage = null;
        }
    }
}
