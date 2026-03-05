<?php

namespace App\Enums;

enum ReturnStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    /**
     * Get human-readable label in Indonesian
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Persetujuan',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
        };
    }

    /**
     * Get Tailwind CSS classes for badge
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::APPROVED => 'bg-green-100 text-green-800',
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::REJECTED => 'bg-red-100 text-red-800',
        };
    }
}
