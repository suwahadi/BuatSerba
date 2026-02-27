<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';
    case EXPIRED = 'expired';

    /**
     * Get human-readable label in Indonesian
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::PAID => 'Lunas',
            self::FAILED => 'Pembayaran Gagal',
            self::EXPIRED => 'Pembayaran Kedaluwarsa',
        };
    }

    /**
     * Get short label for badge
     */
    public function shortLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::PAID => 'Lunas',
            self::FAILED => 'Gagal',
            self::EXPIRED => 'Kedaluwarsa',
        };
    }

    /**
     * Get Tailwind CSS classes for badge
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::PAID => 'bg-green-100 text-green-800',
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::FAILED, self::EXPIRED => 'bg-red-100 text-red-800',
        };
    }
}
