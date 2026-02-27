<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case FAILED = 'failed';
    case PAYMENT_FAILED = 'payment_failed';
    case EXPIRED = 'expired';

    /**
     * Get human-readable label in Indonesian
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::PROCESSING => 'Diproses',
            self::SHIPPED => 'Dikirim',
            self::DELIVERED => 'Diterima',
            self::COMPLETED => 'Selesai',
            self::CANCELLED => 'Dibatalkan',
            self::FAILED => 'Gagal',
            self::PAYMENT_FAILED => 'Pembayaran Gagal',
            self::EXPIRED => 'Kedaluwarsa',
        };
    }

    /**
     * Get color for badge display
     */
    public function color(): string
    {
        return match ($this) {
            self::DELIVERED, self::COMPLETED => 'green',
            self::SHIPPED => 'blue',
            self::PROCESSING => 'orange',
            self::PENDING => 'yellow',
            self::FAILED, self::PAYMENT_FAILED, self::CANCELLED, self::EXPIRED => 'red',
        };
    }

    /**
     * Get Tailwind CSS classes for badge
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::DELIVERED, self::COMPLETED => 'bg-green-100 text-green-800',
            self::SHIPPED => 'bg-blue-100 text-blue-800',
            self::PROCESSING => 'bg-orange-100 text-orange-800',
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::FAILED, self::PAYMENT_FAILED, self::CANCELLED, self::EXPIRED => 'bg-red-100 text-red-800',
        };
    }
}
