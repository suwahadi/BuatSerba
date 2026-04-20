<?php

namespace App\Services;

use App\Models\PaymentMethod;
use App\Models\PremiumMembership;
use Illuminate\Support\Facades\Http;

class PremiumMembershipPaymentService
{
    protected $serverKey;
    protected $clientKey;
    protected $isProduction;
    protected $coreApiUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->clientKey = config('midtrans.client_key');

        $isProductionConfig = config('midtrans.is_production', false);
        if (is_bool($isProductionConfig)) {
            $this->isProduction = $isProductionConfig;
        } else {
            $this->isProduction = filter_var($isProductionConfig, FILTER_VALIDATE_BOOLEAN);
        }

        $this->coreApiUrl = $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * Create transaction for premium membership using Midtrans Core API
     */
    public function createTransaction(PremiumMembership $membership, string $paymentMethod)
    {
        if (is_string($paymentMethod)) {
            $methodParts = explode('-', $paymentMethod);
            $method = $methodParts[0] . (isset($methodParts[1]) ? '-' . $methodParts[1] : '');
            $subMethod = isset($methodParts[2]) ? $methodParts[2] : null;

            $paymentMethodObj = new PaymentMethod($method, $subMethod);
        } else {
            $paymentMethodObj = $paymentMethod;
        }

        $config = $paymentMethodObj->getCoreApiConfig();

        $notificationUrl = config('midtrans.core_api.notification_url');
        if ($notificationUrl && !preg_match('#^https?://#i', $notificationUrl)) {
            $notificationUrl = url($notificationUrl);
        }

        $user = $membership->user;
        $orderId = 'PREM-' . strtoupper(substr(md5($membership->id . time()), 0, 8));

        $payload = [
            'payment_type' => $config['payment_type'],
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $membership->price,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email ?? 'customer@example.com',
                'phone' => $user->phone ?? '08123456789',
            ],
            'callbacks' => [
                'notification_url' => $notificationUrl,
            ],
            'custom_field1' => 'premium_membership',
            'custom_field2' => $membership->id,
        ];

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

        if ($config['payment_type'] === 'echannel') {
            $payload['echannel'] = [
                'bill_info1' => 'Payment for',
                'bill_info2' => 'Premium Membership',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            ])->post($this->coreApiUrl . '/charge', $payload);

            $result = $response->json();

            if ($response->successful() && isset($result['status_code']) && in_array($result['status_code'], ['200', '201'])) {
                $instructions = $this->extractPaymentInstructions($result);

                $this->updateMembershipWithPaymentData($membership, $result, $paymentMethodObj, $orderId);

                return [
                    'success' => true,
                    'data' => $result,
                    'payment_instructions' => $instructions,
                    'membership_id' => $membership->id,
                    'redirect_url' => $result['redirect_url'] ?? null,
                    'order_id' => $orderId,
                ];
            }

            return [
                'success' => false,
                'message' => $this->getFriendlyErrorMessage($result['status_message'] ?? 'Payment creation failed', $result['status_code'] ?? null),
                'error_details' => $result,
            ];

        } catch (\Exception $e) {
            \Log::error('Midtrans Core API Error for Premium Membership: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Payment service unavailable: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update membership with payment data
     */
    protected function updateMembershipWithPaymentData(
        PremiumMembership $membership,
        array $transactionData,
        PaymentMethod $paymentMethodObj,
        string $orderId
    ): void {
        $membership->update([
            'payment_gateway' => 'midtrans',
            'transaction_id' => $transactionData['transaction_id'] ?? $orderId,
            'transaction_time' => $transactionData['transaction_time'] ?? now(),
            'transaction_status' => $transactionData['transaction_status'] ?? 'pending',
            'fraud_status' => $transactionData['fraud_status'] ?? 'accept',
            'payment_type' => $transactionData['payment_type'] ?? $paymentMethodObj->getCoreApiConfig()['payment_type'],
            'payment_channel' => $this->getPaymentChannel($transactionData, $paymentMethodObj),
            'midtrans_response' => $transactionData,
            'signature_key' => $transactionData['signature_key'] ?? null,
            'status_code' => $transactionData['status_code'] ?? null,
            'status_message' => $transactionData['status_message'] ?? null,
        ]);
    }

    /**
     * Determine payment channel from transaction data
     */
    protected function getPaymentChannel($transactionData, $paymentMethodObj)
    {
        if (isset($transactionData['bill_key']) && isset($transactionData['biller_code'])) {
            return 'mandiri';
        }

        if (isset($transactionData['permata_va_number'])) {
            return 'permata';
        }

        if (isset($transactionData['va_numbers']) && is_array($transactionData['va_numbers']) && count($transactionData['va_numbers']) > 0) {
            return $transactionData['va_numbers'][0]['bank'];
        }

        if (isset($transactionData['bank_transfer']['bank'])) {
            return $transactionData['bank_transfer']['bank'];
        }

        if (isset($transactionData['payment_type'])) {
            return $transactionData['payment_type'];
        }

        $config = $paymentMethodObj->getCoreApiConfig();
        if (isset($config['bank'])) {
            return $config['bank'];
        }

        return $paymentMethodObj->getMethod();
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
                if (isset($transactionData['permata_va_number'])) {
                    $instructions = [
                        'type' => 'virtual_account',
                        'bank' => 'permata',
                        'va_number' => $transactionData['permata_va_number'],
                        'instructions' => $this->getVAInstructions('permata'),
                    ];
                } elseif (isset($transactionData['va_numbers'][0])) {
                    $vaNumber = $transactionData['va_numbers'][0];
                    $instructions = [
                        'type' => 'virtual_account',
                        'bank' => $vaNumber['bank'],
                        'va_number' => $vaNumber['va_number'],
                        'instructions' => $this->getVAInstructions($vaNumber['bank']),
                    ];
                } elseif (isset($transactionData['bill_key']) && isset($transactionData['biller_code'])) {
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

    /**
     * Convert technical Midtrans error messages to user-friendly messages
     */
    private function getFriendlyErrorMessage(string $technicalMessage, ?string $statusCode = null): string
    {
        $errorMappings = [
            'Payment channel is not activated' => 'Metode pembayaran ini sedang tidak tersedia. Silakan pilih metode pembayaran lain.',
            'Transaction is denied' => 'Transaksi ditolak. Silakan coba lagi atau gunakan metode pembayaran lain.',
            'Invalid transaction data' => 'Data transaksi tidak valid. Silakan periksa kembali data pembayaran Anda.',
            'Duplicate order id' => 'Pesanan dengan nomor ini sudah ada. Silakan buat pesanan baru.',
            'Invalid API key' => 'Terjadi kesalahan konfigurasi pembayaran. Silakan hubungi admin.',
            'Access denied' => 'Akses ditolak. Silakan hubungi admin untuk bantuan.',
            'Merchant not found' => 'Konfigurasi pembayaran tidak ditemukan. Silakan hubungi admin.',
            'Transaction not found' => 'Transaksi tidak ditemukan. Silakan coba lagi.',
            'Expired transaction' => 'Waktu transaksi telah habis. Silakan buat pesanan baru.',
            'Transaction already processed' => 'Transaksi sudah diproses sebelumnya.',
            'Invalid amount' => 'Jumlah pembayaran tidak valid. Silakan periksa kembali.',
            'Bank is not supported' => 'Bank yang dipilih tidak didukung. Silakan pilih bank lain.',
        ];

        $statusCodeMappings = [
            '402' => 'Metode pembayaran ini sedang tidak tersedia. Silakan pilih metode pembayaran lain.',
            '500' => 'Layanan pembayaran sedang gangguan. Silakan coba beberapa saat lagi.',
            '503' => 'Layanan pembayaran sedang dalam pemeliharaan. Silakan coba beberapa saat lagi.',
        ];

        foreach ($errorMappings as $technical => $friendly) {
            if (stripos($technicalMessage, $technical) !== false) {
                return $friendly;
            }
        }

        if ($statusCode && isset($statusCodeMappings[$statusCode])) {
            return $statusCodeMappings[$statusCode];
        }

        return 'Gagal memproses pembayaran. Silakan coba metode pembayaran lain atau hubungi customer service kami.';
    }
}
