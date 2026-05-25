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

    /**
     * Bank accounts untuk manual transfer, dirakit dari key global_config('manual_bank_*').
     * Mengembalikan array of accounts agar view yang foreach (mis. premium purchase modal)
     * tetap bisa di-extend bila nanti ada multi-rekening, tanpa mengubah template.
     * Return [] bila konfigurasi belum diisi (view akan fallback ke pesan "hubungi admin").
     */
    public static function getBankAccounts(): array
    {
        $bankName = trim((string) global_config('manual_bank_name', ''));
        $accountNumber = trim((string) global_config('manual_bank_account_number', ''));
        $accountName = trim((string) global_config('manual_bank_account_name', ''));

        if ($bankName === '' && $accountNumber === '' && $accountName === '') {
            return [];
        }

        return [
            [
                'bank_name' => $bankName,
                'account_number' => $accountNumber,
                'account_name' => $accountName,
            ],
        ];
    }
}
