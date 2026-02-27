<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case MEMBER_BALANCE = 'member_balance';
    case TRANSFER = 'transfer';
    case BANK_TRANSFER = 'bank_transfer';
    case E_WALLET = 'e_wallet';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Tunai',
            self::MEMBER_BALANCE => 'Saldo Member',
            self::TRANSFER => 'Transfer Bank',
            self::BANK_TRANSFER => 'Transfer Bank',
            self::E_WALLET => 'E-Wallet',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::CASH => 'Pembayaran tunai saat pengiriman',
            self::MEMBER_BALANCE => 'Pembayaran menggunakan saldo member',
            self::TRANSFER => 'Pembayaran melalui transfer bank',
            self::BANK_TRANSFER => 'Pembayaran melalui transfer bank',
            self::E_WALLET => 'Pembayaran melalui e-wallet',
        };
    }

    public static function fromValue(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        return null;
    }
}
