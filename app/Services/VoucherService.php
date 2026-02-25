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

        // 1. Validate Period (Start & End)
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

        // 2. Validate Global Usage
        if ($voucher->usage_limit !== null && $voucher->usage_count >= $voucher->usage_limit) {
            return [
                'success' => false,
                'message' => 'Kuota voucher telah habis.',
            ];
        }

        // 3. Validate Specific User
        if ($voucher->user_id && (! $user || $user->id !== $voucher->user_id)) {
            return [
                'success' => false,
                'message' => 'Voucher ini tidak berlaku untuk akun Anda.',
            ];
        }

        // 4. Validate Minimum Spend
        if ($subtotal < $voucher->min_spend) {
            return [
                'success' => false,
                'message' => 'Minimal belanja Rp ' . number_format($voucher->min_spend, 0, ',', '.') . ' untuk menggunakan voucher ini.',
            ];
        }

        // 5. Validate New User Only
        if ($voucher->is_new_user_only) {
            if (! $user) {
                return [
                    'success' => false,
                    'message' => 'Silakan login untuk menggunakan voucher pengguna baru.',
                ];
            }
            
            // Check if user has any existing orders (regardless of status, usually completed ones count, but strictly 'first order' implies none exist)
            // We check for any order to be strict.
            $hasOrders = \App\Models\Order::where('user_id', $user->id)->exists();
            if ($hasOrders) {
                return [
                    'success' => false,
                    'message' => 'Voucher ini hanya berlaku untuk transaksi pertama.',
                ];
            }
        }

        // 6. Validate Limit Per User
        // Note: This requires tracking per-user usage in the database (e.g., voucher_id on orders table).
        // Since the current orders table structure doesn't support this relation directly, 
        // we skip the strict DB check to prevent errors, but this is where it would go.
        /*
        if ($user && $voucher->limit_per_user > 0) {
            $userUsage = \App\Models\Order::where('user_id', $user->id)->where('voucher_code', $code)->count();
            if ($userUsage >= $voucher->limit_per_user) {
                 return ['success' => false, 'message' => 'Anda sudah mencapai batas penggunaan voucher ini.'];
            }
        }
        */

        // 7. Calculate Discount
        $discountAmount = 0;
        if ($voucher->is_free_shipment) {
            // If free shipment, the amount is used for shipping subsidy, not product discount
            $discountAmount = 0;
        } elseif ($voucher->type === 'percentage') {
            $discountAmount = $subtotal * ($voucher->amount / 100);
            
            // Apply Max Discount Cap
            if ($voucher->max_discount_amount && $discountAmount > $voucher->max_discount_amount) {
                $discountAmount = $voucher->max_discount_amount;
            }
        } else {
            // Fixed amount
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
                'min_spend' => $voucher->min_spend,
                'is_free_shipment' => (bool) $voucher->is_free_shipment,
                'cashback_type' => $voucher->cashback_type,
                'cashback_amount' => $voucher->cashback_amount,
                'cashback_percentage' => $voucher->cashback_percentage,
                'cashback_value' => $this->calculateCashback($voucher, $subtotal),
            ],
        ];
    }

    /**
     * Calculate cashback amount from voucher
     */
    public function calculateCashback(Voucher $voucher, float $subtotal): float
    {
        if (!$voucher->hasCashback()) {
            return 0;
        }

        $cashbackAmount = 0;

        if ($voucher->cashback_type === 'fixed') {
            $cashbackAmount = $voucher->cashback_amount;
        } elseif ($voucher->cashback_type === 'percentage') {
            $cashbackAmount = $subtotal * ($voucher->cashback_percentage / 100);
        }

        return $cashbackAmount;
    }

    /**
     * Process cashback for an order
     */
    public function processCashback(int $userId, Voucher $voucher, float $subtotal, int $orderId): void
    {
        if (!$voucher->hasCashback()) {
            return;
        }

        $cashbackAmount = $this->calculateCashback($voucher, $subtotal);

        if ($cashbackAmount > 0) {
            $memberWalletService = new MemberWalletService();
            $memberWalletService->creditCashback(
                $userId,
                $cashbackAmount,
                $orderId,
                'Cashback dari voucher ' . $voucher->voucher_code
            );
        }
    }
}
