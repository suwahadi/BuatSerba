<?php

namespace App\Models;

class PaymentMethod
{
    protected $method;
    protected $subMethod;
    protected $config;

    public function __construct($method, $subMethod = null)
    {
        $this->method = $method;
        $this->subMethod = $subMethod;
        $this->config = $this->getConfig();
    }

    public function getCoreApiConfig()
    {
        switch ($this->method) {
            case 'bank-transfer':
                $bank = $this->subMethod ?? 'bca';
                return [
                    'payment_type' => 'bank_transfer',
                    'bank' => $bank
                ];
            
            case 'e-wallet':
                $wallet = $this->subMethod ?? 'gopay';
                return [
                    'payment_type' => $wallet
                ];
            
            case 'credit-card':
                return [
                    'payment_type' => 'credit_card'
                ];
            
            case 'qris':
                return [
                    'payment_type' => 'qris'
                ];
            
            case 'cod':
                return [
                    'payment_type' => 'cod'
                ];
            
            default:
                return [
                    'payment_type' => 'bank_transfer',
                    'bank' => 'bca'
                ];
        }
    }

    public function getSnapConfig()
    {
        switch ($this->method) {
            case 'bank-transfer':
                $bank = $this->subMethod ?? 'bca';
                return [
                    'payment_type' => 'bank_transfer',
                    'bank_transfer' => [
                        'bank' => $bank
                    ]
                ];
            
            case 'e-wallet':
                $wallet = $this->subMethod ?? 'gopay';
                $config = [
                    'payment_type' => 'echannel'
                ];
                
                switch ($wallet) {
                    case 'gopay':
                        $config['gopay'] = [
                            'enable_callback' => true,
                            'callback_url' => config('midtrans.finish_url')
                        ];
                        break;
                    case 'shopeepay':
                        $config['shopeepay'] = [
                            'callback_url' => config('midtrans.finish_url')
                        ];
                        break;
                }
                
                return $config;
            
            case 'credit-card':
                return [
                    'payment_type' => 'credit_card',
                    'credit_card' => [
                        'secure' => config('midtrans.is_3ds', true)
                    ]
                ];
            
            case 'qris':
                return [
                    'payment_type' => 'qris'
                ];
            
            case 'cod':
                return [
                    'payment_type' => 'cod'
                ];
            
            default:
                return [
                    'payment_type' => 'bank_transfer',
                    'bank_transfer' => [
                        'bank' => 'bca'
                    ]
                ];
        }
    }

    private function getConfig()
    {
        $configs = [
            'bank-transfer' => [
                'types' => ['bca', 'bni', 'bri', 'mandiri'],
                'default' => 'bca'
            ],
            'e-wallet' => [
                'types' => ['gopay', 'shopeepay', 'dana', 'ovo', 'linkaja'],
                'default' => 'gopay'
            ]
        ];

        return $configs[$this->method] ?? [];
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getSubMethod()
    {
        return $this->subMethod;
    }

    public function isBankTransfer()
    {
        return $this->method === 'bank-transfer';
    }

    public function isEwallet()
    {
        return $this->method === 'e-wallet';
    }

    public function isCreditCard()
    {
        return $this->method === 'credit-card';
    }

    public function isQris()
    {
        return $this->method === 'qris';
    }

    public function isCod()
    {
        return $this->method === 'cod';
    }
}