<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Http;

class MidtransService
{
    protected $serverKey;

    protected $clientKey;

    protected $isProduction;

    protected $coreApiUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->clientKey = config('midtrans.client_key');
        $this->isProduction = config('midtrans.is_production', false);
        $this->coreApiUrl = $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * Create transaction using Midtrans Core API
     * This method handles payment method configuration and creates the transaction
     */
    public function createTransaction($order, $paymentMethod)
    {
        // Handle payment method as string or object
        if (is_string($paymentMethod)) {
            $methodParts = explode('-', $paymentMethod);
            $method = $methodParts[0].(isset($methodParts[1]) ? '-'.$methodParts[1] : '');
            $subMethod = isset($methodParts[2]) ? $methodParts[2] : null;

            $paymentMethodObj = new PaymentMethod($method, $subMethod);
        } else {
            $paymentMethodObj = $paymentMethod;
        }

        $config = $paymentMethodObj->getCoreApiConfig();

        // Build base payload
        $payload = [
            'payment_type' => $config['payment_type'],
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->customer_email ?? 'customer@example.com',
                'phone' => $order->customer_phone ?? '08123456789',
            ],
            // Add notification callback URL for Midtrans to notify payment status
            'callbacks' => [
                'notification_url' => config('midtrans.core_api.notification_url'),
            ],
        ];

        // Add specific payment method configurations
        if (isset($config['bank'])) {
            $payload['bank_transfer'] = ['bank' => $config['bank']];
        }

        if (isset($config['va_number'])) {
            $payload['bank_transfer']['va_number'] = $config['va_number'];
        }

        if (isset($config['gopay'])) {
            $payload['gopay'] = $config['gopay'];
        }

        if (isset($config['shopeepay'])) {
            $payload['shopeepay'] = $config['shopeepay'];
        }

        try {
            \Log::info('Midtrans Core API Request: '.$this->coreApiUrl.'/charge', ['payload' => $payload]);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic '.base64_encode($this->serverKey.':'),
            ])->post($this->coreApiUrl.'/charge', $payload);

            $result = $response->json();

            \Log::info('Midtrans Core API Response: '.$response->status(), ['response' => $result]);

            if ($response->successful() && isset($result['status_code']) && in_array($result['status_code'], ['200', '201'])) {
                // Extract payment instructions
                $instructions = $this->extractPaymentInstructions($result);

                // Create payment record in database
                $payment = $this->createPaymentRecord($order, $result, $paymentMethodObj);

                return [
                    'success' => true,
                    'data' => $result,
                    'payment_instructions' => $instructions,
                    'payment_id' => $payment->id,
                    'redirect_url' => $result['redirect_url'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $result['status_message'] ?? 'Payment creation failed',
                'error_details' => $result,
            ];

        } catch (\Exception $e) {
            \Log::error('Midtrans Core API Error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Payment service unavailable: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Create payment record in database
     */
    protected function createPaymentRecord($order, $transactionData, $paymentMethodObj)
    {
        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_gateway' => 'midtrans',
            'transaction_id' => $transactionData['transaction_id'] ?? $transactionData['order_id'] ?? null,
            'transaction_time' => $transactionData['transaction_time'] ?? now(),
            'transaction_status' => $transactionData['transaction_status'] ?? 'pending',
            'fraud_status' => $transactionData['fraud_status'] ?? 'accept',
            'payment_type' => $transactionData['payment_type'] ?? $paymentMethodObj->getCoreApiConfig()['payment_type'],
            'payment_channel' => $this->getPaymentChannel($transactionData, $paymentMethodObj),
            'gross_amount' => $transactionData['gross_amount'] ?? $order->total,
            'currency' => $transactionData['currency'] ?? config('midtrans.core_api.currency', 'IDR'),
            'signature_key' => $transactionData['signature_key'] ?? null,
            'status_code' => $transactionData['status_code'] ?? null,
            'status_message' => $transactionData['status_message'] ?? null,
            'midtrans_response' => $transactionData,
            'expired_at' => $order->payment_deadline,
        ]);

        return $payment;
    }

    /**
     * Determine payment channel from transaction data
     * Extract bank name from Midtrans response (e.g., from va_numbers array)
     */
    protected function getPaymentChannel($transactionData, $paymentMethodObj)
    {
        // Priority 1: Get from permata_va_number (Permata specific)
        if (isset($transactionData['permata_va_number'])) {
            \Log::info('Payment channel detected: permata', ['permata_va_number' => $transactionData['permata_va_number']]);

            return 'permata';
        }

        // Priority 2: Get from va_numbers array (most accurate from Midtrans)
        if (isset($transactionData['va_numbers']) && is_array($transactionData['va_numbers']) && count($transactionData['va_numbers']) > 0) {
            $channel = $transactionData['va_numbers'][0]['bank'];
            \Log::info('Payment channel detected from va_numbers', ['channel' => $channel]);

            return $channel;
        }

        // Priority 3: Get from bank_transfer
        if (isset($transactionData['bank_transfer']['bank'])) {
            return $transactionData['bank_transfer']['bank'];
        }

        // Priority 4: Get from payment_type
        if (isset($transactionData['payment_type'])) {
            $channel = $transactionData['payment_type'];
            \Log::info('Payment channel fallback to payment_type', ['channel' => $channel]);

            return $channel;
        }

        // Priority 5: Fallback to config
        $config = $paymentMethodObj->getCoreApiConfig();
        if (isset($config['bank'])) {
            return $config['bank'];
        }

        return $paymentMethodObj->getMethod();
    }

    /**
     * Get transaction status from Midtrans
     */
    public function getTransactionStatus($orderId)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Basic '.base64_encode($this->serverKey.':'),
            ])->get($this->coreApiUrl.'/'.$orderId.'/status');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get transaction status',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Status check unavailable: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Extract payment instructions from Midtrans transaction response
     */
    public function extractPaymentInstructions($transactionData)
    {
        $paymentType = $transactionData['payment_type'] ?? null;
        $instructions = [];

        switch ($paymentType) {
            case 'bank_transfer':
                // Handle Permata VA (different response structure)
                if (isset($transactionData['permata_va_number'])) {
                    $instructions = [
                        'type' => 'virtual_account',
                        'bank' => 'permata',
                        'va_number' => $transactionData['permata_va_number'],
                        'instructions' => $this->getVAInstructions('permata'),
                    ];
                }
                // Handle other banks (BCA, BNI, BRI, etc.)
                elseif (isset($transactionData['va_numbers'][0])) {
                    $vaNumber = $transactionData['va_numbers'][0];
                    $instructions = [
                        'type' => 'virtual_account',
                        'bank' => $vaNumber['bank'],
                        'va_number' => $vaNumber['va_number'],
                        'instructions' => $this->getVAInstructions($vaNumber['bank']),
                    ];
                }
                // Handle Mandiri Bill Payment
                elseif (isset($transactionData['bill_key']) && isset($transactionData['biller_code'])) {
                    $instructions = [
                        'type' => 'mandiri_echannel',
                        'bill_key' => $transactionData['bill_key'],
                        'biller_code' => $transactionData['biller_code'],
                        'instructions' => $this->getMandiriInstructions(),
                    ];
                }
                break;

            case 'echannel':
                $instructions = [
                    'type' => 'mandiri_echannel',
                    'bill_key' => $transactionData['bill_key'] ?? null,
                    'biller_code' => $transactionData['biller_code'] ?? null,
                    'instructions' => $this->getMandiriInstructions(),
                ];
                break;

            case 'qris':
                $instructions = [
                    'type' => 'qris',
                    'qr_string' => $transactionData['qr_string'] ?? null,
                    'expiry_time' => $transactionData['expiry_time'] ?? null,
                    'actions' => $transactionData['actions'] ?? [],
                    'instructions' => $this->getQrisInstructions(),
                ];
                break;

            case 'gopay':
            case 'shopeepay':
            case 'dana':
            case 'linkaja':
            case 'ovo':
                $instructions = [
                    'type' => 'ewallet',
                    'provider' => $paymentType,
                    'actions' => $transactionData['actions'] ?? [],
                    'instructions' => $this->getEwalletInstructions($paymentType),
                ];
                break;

            case 'credit_card':
                $instructions = [
                    'type' => 'credit_card',
                    'provider' => 'Credit Card',
                    'instructions' => $this->getCreditCardInstructions(),
                ];
                break;
        }

        return $instructions;
    }

    private function getVAInstructions($bank)
    {
        $instructions = [
            'bca' => [
                'Pilih menu m-BCA di aplikasi BCA mobile',
                'Pilih m-Transfer > BCA Virtual Account',
                'Masukkan nomor Virtual Account di atas',
                'Masukkan jumlah yang harus dibayar',
                'Ikuti instruksi untuk menyelesaikan pembayaran',
            ],
            'bni' => [
                'Masuk ke ATM BNI atau BNI Mobile Banking',
                'Pilih Menu Transfer > Virtual Account Billing',
                'Masukkan nomor Virtual Account di atas',
                'Masukkan jumlah yang harus dibayar',
                'Ikuti instruksi untuk menyelesaikan pembayaran',
            ],
            'bri' => [
                'Masuk ke ATM BRI atau BRI Mobile Banking',
                'Pilih Menu Pembayaran > BRIVA',
                'Masukkan nomor Virtual Account di atas',
                'Masukkan jumlah yang harus dibayar',
                'Ikuti instruksi untuk menyelesaikan pembayaran',
            ],
            'permata' => [
                'Masuk ke ATM Permata atau PermataNet/PermataMobile',
                'Pilih Menu Transaksi Lainnya > Pembayaran > Pembayaran Lainnya > Virtual Account',
                'Masukkan nomor Virtual Account di atas',
                'Masukkan jumlah yang harus dibayar',
                'Ikuti instruksi untuk menyelesaikan pembayaran',
            ],
            'mandiri' => [
                'Masuk ke ATM Mandiri atau Mandiri Online',
                'Pilih Menu Bayar/Beli > Multi Payment',
                'Masukkan Kode Perusahaan (70012)',
                'Masukkan Kode Pembayaran yang tertera di atas',
                'Ikuti instruksi untuk menyelesaikan pembayaran',
            ],
        ];

        return $instructions[$bank] ?? [
            'Gunakan nomor Virtual Account di atas untuk melakukan pembayaran',
            'Pembayaran dapat dilakukan melalui ATM, mobile banking, atau internet banking',
            'Masukkan nomor Virtual Account sebagai nomor tujuan',
            'Masukkan jumlah yang harus dibayar sesuai total pesanan',
        ];
    }

    private function getMandiriInstructions()
    {
        return [
            'Masuk ke ATM Mandiri atau Mandiri Online',
            'Pilih Menu Bayar/Beli > Multi Payment',
            'Masukkan Kode Perusahaan (70012)',
            'Masukkan Kode Pembayaran yang tertera di atas',
            'Ikuti instruksi untuk menyelesaikan pembayaran',
        ];
    }

    private function getQrisInstructions()
    {
        return [
            'Buka aplikasi e-wallet atau mobile banking Anda',
            'Pilih menu Scan QR atau QRIS',
            'Arahkan kamera ke kode QR di atas',
            'Periksa detail pembayaran',
            'Konfirmasi pembayaran',
        ];
    }

    private function getEwalletInstructions($provider)
    {
        $instructions = [
            'gopay' => [
                'Buka aplikasi Gojek',
                'Klik notifikasi pembayaran atau buka GoPay',
                'Periksa detail pembayaran',
                'Konfirmasi pembayaran dengan PIN GoPay',
            ],
            'shopeepay' => [
                'Buka aplikasi Shopee',
                'Klik notifikasi pembayaran atau buka ShopeePay',
                'Periksa detail pembayaran',
                'Konfirmasi pembayaran dengan PIN ShopeePay',
            ],
            'dana' => [
                'Buka aplikasi DANA',
                'Klik notifikasi pembayaran',
                'Periksa detail pembayaran',
                'Konfirmasi pembayaran dengan PIN DANA',
            ],
            'ovo' => [
                'Buka aplikasi OVO',
                'Klik notifikasi pembayaran',
                'Periksa detail pembayaran',
                'Konfirmasi pembayaran dengan PIN OVO',
            ],
            'linkaja' => [
                'Buka aplikasi LinkAja',
                'Klik notifikasi pembayaran',
                'Periksa detail pembayaran',
                'Konfirmasi pembayaran dengan PIN LinkAja',
            ],
        ];

        return $instructions[$provider] ?? [
            'Buka aplikasi e-wallet Anda',
            'Ikuti notifikasi pembayaran yang muncul',
            'Periksa detail pembayaran',
            'Konfirmasi pembayaran dengan PIN',
        ];
    }

    private function getCreditCardInstructions()
    {
        return [
            'Masukkan detail kartu kredit Anda',
            'Pastikan informasi kartu benar',
            'Konfirmasi pembayaran dengan memasukkan CVV',
            'Ikuti instruksi otentikasi jika diperlukan (3D Secure)',
        ];
    }
}
