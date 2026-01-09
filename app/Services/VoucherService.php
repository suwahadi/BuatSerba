<?php

namespace App\Services;

use App\Models\User;
use App\Models\Voucher;
use Carbon\Carbon;

class VoucherService
{
    /**
     * Validate and calculate voucher discount
     */
    public function applyVoucher(string $code, float $subtotal, ?User $user = null): array
    {
        $voucher = Voucher::where('voucher_code', $code)
            ->where('is_active', true)
            ->first();

        if (! $voucher) {
            return [
                'success' => false,
                'message' => 'Kode voucher tidak valid.',
            ];
        }

        // Validate Date
        $now = Carbon::now();
        if ($voucher->valid_start && $now->lt($voucher->valid_start)) {
            return [
                'success' => false,
                'message' => 'Promo belum dimulai.',
            ];
        }

        if ($voucher->valid_end && $now->gt($voucher->valid_end)) {
            return [
                'success' => false,
                'message' => 'Promo sudah berakhir.',
            ];
        }

        // Validate Specific User
        if ($voucher->user_id && (! $user || $user->id !== $voucher->user_id)) {
            return [
                'success' => false,
                'message' => 'Voucher ini tidak berlaku untuk akun Anda.',
            ];
        }

        // Calculate Discount
        $discountAmount = 0;
        if ($voucher->type === 'percentage') {
            $discountAmount = $subtotal * ($voucher->amount / 100);
        } else {
            $discountAmount = $voucher->amount;
        }

        // Ensure discount doesn't exceed subtotal
        if ($discountAmount > $subtotal) {
            $discountAmount = $subtotal;
        }

        return [
            'success' => true,
            'message' => 'Voucher berhasil digunakan!',
            'data' => [
                'voucher_id' => $voucher->id,
                'code' => $voucher->voucher_code,
                'type' => $voucher->type,
                'amount_value' => $voucher->amount,
                'discount_amount' => $discountAmount,
                'is_free_shipment' => (bool) $voucher->is_free_shipment,
            ],
        ];
    }
}
