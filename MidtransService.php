<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MidtransService
{
    protected $serverKey;

    protected $isProduction;

    protected $coreApiUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->isProduction = config('midtrans.is_production', false);
        $this->coreApiUrl = $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    public function createTransaction($order, $paymentMethod)
    {
        $config = $paymentMethod->getCoreApiConfig();

        $payload = [
            'payment_type' => $config['payment_type'],
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->customer_email ?? 'customer@example.com',
                'phone' => $order->customer_phone ?? '08123456789',
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
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic '.base64_encode($this->serverKey.':'),
            ])->post($this->coreApiUrl.'/charge', $payload);

            $result = $response->json();

            if ($response->successful() && isset($result['status_code']) && $result['status_code'] == '201') {
                // Extract payment instructions
                $instructions = $this->extractPaymentInstructions($result);

                return [
                    'success' => true,
                    'data' => $result,
                    'payment_instructions' => $instructions,
                    'payment_data' => $result,
                ];
            }

            return [
                'success' => false,
                'message' => $result['status_message'] ?? 'Payment creation failed',
                'error_details' => $result,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Payment service unavailable',
            ];
        }
    }

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
                'message' => 'Status check unavailable',
            ];
        }
    }

    public function extractPaymentInstructions($transactionData)
    {
        $paymentType = $transactionData['payment_type'] ?? null;
        $instructions = [];

        switch ($paymentType) {
            case 'bank_transfer':
                if (isset($transactionData['va_numbers'][0])) {
                    $vaNumber = $transactionData['va_numbers'][0];
                    $instructions = [
                        'type' => 'virtual_account',
                        'bank' => $vaNumber['bank'],
                        'va_number' => $vaNumber['va_number'],
                        'instructions' => $this->getVAInstructions($vaNumber['bank']),
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
        ];

        return $instructions[$provider] ?? [
            'Buka aplikasi e-wallet Anda',
            'Ikuti notifikasi pembayaran yang muncul',
            'Periksa detail pembayaran',
            'Konfirmasi pembayaran dengan PIN',
        ];
    }
}
