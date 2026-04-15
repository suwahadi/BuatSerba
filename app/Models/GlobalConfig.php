<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalConfig extends Model
{
    protected $fillable = [
        'key',
        'value',
        'description',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'sort' => 'integer',
            'value' => 'string',
        ];
    }

    public static function getCashbackPercentage(): float
    {
        $percentage = (float) global_config('cashback', 1);
        return $percentage / 100;
    }

    public static function getPremiumMembershipPrice(): int
    {
        return (int) global_config('premium_membership_price', 100000);
    }

    public static function MaintenanceMode(): int
    {
        return (int) global_config('maintenance_mode', 1);
    }
}
