<?php

namespace App\Services;

use App\Exceptions\Wallet\DuplicateTransactionException;
use App\Exceptions\Wallet\InsufficientBalanceException;
use App\Models\MemberBalanceLedger;
use App\Models\MemberWallet;
use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MemberWalletService
{
    /**
     * Get or create a wallet for a user.
     */
    public function getOrCreateWallet(User $user): MemberWallet
    {
        return MemberWallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'locked_balance' => 0]
        );
    }

    /**
     * Get or create a wallet for a user by user ID.
     */
    public function getOrCreateWalletById(int $userId): MemberWallet
    {
        return MemberWallet::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'locked_balance' => 0]
        );
    }

    /**
     * Get the available balance for a user.
     */
    public function getBalance(User $user): float
    {
        return $this->getOrCreateWallet($user)->available_balance;
    }

    /**
     * Get the available balance for a user by user ID.
     */
    public function getBalanceByUserId(int $userId): float
    {
        $wallet = MemberWallet::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'locked_balance' => 0]
        );
        
        return $wallet->available_balance;
    }

    /**
     * Credit (add) balance to a user's wallet.
     *
     * @throws DuplicateTransactionException
     */
    public function credit(User $user, float $amount, string $sourceType, ?int $sourceId, string $description, string $referenceCode): MemberBalanceLedger
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Amount must be greater than zero.");
        }

        // Check for duplicate transaction before locking to fail fast
        if (MemberBalanceLedger::where('reference_code', $referenceCode)->exists()) {
            throw new DuplicateTransactionException();
        }

        return DB::transaction(function () use ($user, $amount, $sourceType, $sourceId, $description, $referenceCode) {
            // Lock the wallet row for update
            $wallet = MemberWallet::where('user_id', $user->id)->lockForUpdate()->first();
            
            if (!$wallet) {
                $wallet = $this->getOrCreateWallet($user);
                $wallet = MemberWallet::where('user_id', $user->id)->lockForUpdate()->first();
            }

            // Check duplicate again inside transaction to prevent race conditions
            if (MemberBalanceLedger::where('reference_code', $referenceCode)->exists()) {
                throw new DuplicateTransactionException();
            }

            $balanceBefore = $wallet->balance;
            $wallet->balance += $amount;
            $wallet->save();

            return MemberBalanceLedger::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'description' => $description,
                'reference_code' => $referenceCode,
            ]);
        });
    }

    /**
     * Debit balance for order payment.
     * 
     * @throws InsufficientBalanceException
     */
    public function debitForOrder(User $user, Order $order): void
    {
        $this->debitForOrderById($user->id, $order->total, $order->id, $order->order_number);
    }

    /**
     * Debit balance for order payment by user ID.
     * 
     * @throws InsufficientBalanceException
     */
    public function debitForOrderById(int $userId, float $amount, int $orderId, string $orderNumber): void
    {
        if ($amount <= 0) {
            return; // Nothing to debit
        }

        $referenceCode = "order-payment-{$orderId}-" . Str::random(6);

        DB::transaction(function () use ($userId, $amount, $orderId, $orderNumber, $referenceCode) {
            $wallet = MemberWallet::where('user_id', $userId)->lockForUpdate()->first();
            
            if (!$wallet) {
                $wallet = MemberWallet::firstOrCreate(
                    ['user_id' => $userId],
                    ['balance' => 0, 'locked_balance' => 0]
                );
            }
            
            if ($wallet->available_balance < $amount) {
                throw new InsufficientBalanceException();
            }

            $balanceBefore = $wallet->balance;
            
            // Decrease balance and increase locked_balance
            $wallet->balance -= $amount;
            $wallet->locked_balance += $amount;
            $wallet->save();

            MemberBalanceLedger::create([
                'user_id' => $userId,
                'type' => 'debit',
                'source_type' => 'order_payment',
                'source_id' => $orderId,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'description' => "Order #{$orderNumber}",
                'reference_code' => $referenceCode,
            ]);
        });
    }

    /**
     * Release locked balance back to available balance when an order is cancelled.
     */
    public function releaseOrderLock(Order $order): void
    {
        $amount = (float) $order->total;
        
        if ($amount <= 0) {
            return;
        }

        // We only release lock for member_balance payment method and unpaid orders
        if ($order->payment_method !== 'member_balance' || $order->payment_status === 'paid') {
            return;
        }

        $user = $order->user;
        if (!$user) {
            return;
        }

        $this->releaseOrderLockById($user->id, $amount, $order->id, $order->order_number);
    }

    /**
     * Release locked balance back to available balance by user ID.
     */
    public function releaseOrderLockById(int $userId, float $amount, int $orderId, string $orderNumber): void
    {
        if ($amount <= 0) {
            return;
        }

        $referenceCode = "order-cancellation-refund-{$orderId}-" . Str::random(6);

        DB::transaction(function () use ($userId, $amount, $orderId, $orderNumber, $referenceCode) {
            $wallet = MemberWallet::where('user_id', $userId)->lockForUpdate()->first();
            
            if (!$wallet || $wallet->locked_balance < $amount) {
                return; // Nothing to release or already released
            }

            $balanceBefore = $wallet->balance;
            
            // Release the lock: decrease locked_balance and add back to balance
            $wallet->locked_balance -= $amount;
            $wallet->balance += $amount;
            $wallet->save();

            MemberBalanceLedger::create([
                'user_id' => $userId,
                'type' => 'credit',
                'source_type' => 'order_cancellation_refund',
                'source_id' => $orderId,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'description' => "Pengembalian saldo untuk pembatalan Order #{$orderNumber}",
                'reference_code' => $referenceCode,
            ]);
        });
    }

    /**
     * Credit cashback from a voucher to the user's wallet.
     */
    public function creditCashback(Order $order, Voucher $voucher): void
    {
        if (!$voucher->hasCashback() || !$order->user_id) {
            return;
        }

        $cashbackAmount = 0;
        if ($voucher->cashback_amount > 0) {
            $cashbackAmount = $voucher->cashback_amount;
        } elseif ($voucher->cashback_percentage > 0) {
            $subtotal = $order->subtotal ?? 0;
            $cashbackAmount = ($subtotal * $voucher->cashback_percentage) / 100;
        }

        if ($cashbackAmount <= 0) {
            return;
        }

        $this->creditCashbackById(
            $order->user_id,
            $cashbackAmount,
            $order->id,
            $order->order_number,
            $voucher->id,
            $voucher->voucher_code
        );
    }

    /**
     * Credit cashback by user ID.
     */
    public function creditCashbackById(int $userId, float $amount, int $orderId, string $orderNumber, int $voucherId, string $voucherCode): void
    {
        if ($amount <= 0) {
            return;
        }

        $referenceCode = "cashback-order-{$orderId}-voucher-{$voucherId}";

        try {
            $wallet = MemberWallet::firstOrCreate(
                ['user_id' => $userId],
                ['balance' => 0, 'locked_balance' => 0]
            );

            $balanceBefore = $wallet->balance;
            
            DB::transaction(function () use ($wallet, $userId, $amount, $orderId, $voucherId, $voucherCode, $referenceCode, $balanceBefore) {
                // Lock the wallet row for update
                $wallet = MemberWallet::where('user_id', $userId)->lockForUpdate()->first();
                
                // Check duplicate again inside transaction to prevent race conditions
                if (MemberBalanceLedger::where('reference_code', $referenceCode)->exists()) {
                    throw new DuplicateTransactionException();
                }

                $wallet->balance += $amount;
                $wallet->save();

                MemberBalanceLedger::create([
                    'user_id' => $userId,
                    'type' => 'credit',
                    'source_type' => 'voucher_cashback',
                    'source_id' => $voucherId,
                    'amount' => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $wallet->balance,
                    'description' => "Cashback dari penggunaan voucher {$voucherCode} pada Order #{$orderNumber}",
                    'reference_code' => $referenceCode,
                ]);
            });
        } catch (DuplicateTransactionException $e) {
            // Ignore duplicate transaction for cashback (idempotent)
        }
    }

    /**
     * Release locked balance when order is completed (paid successfully).
     * This moves the locked amount to the merchant/system (not back to user).
     */
    public function completeOrder(Order $order): void
    {
        $amount = (float) $order->total;
        
        if ($amount <= 0) {
            return;
        }

        // We only process for member_balance payment method and paid orders
        if ($order->payment_method !== 'member_balance' || $order->payment_status !== 'paid') {
            return;
        }

        $user = $order->user;
        if (!$user) {
            return;
        }

        $this->completeOrderById($user->id, $amount, $order->id, $order->order_number);
    }

    /**
     * Complete order by user ID - release locked balance to merchant.
     */
    public function completeOrderById(int $userId, float $amount, int $orderId, string $orderNumber): void
    {
        if ($amount <= 0) {
            return;
        }

        DB::transaction(function () use ($userId, $amount, $orderId, $orderNumber) {
            $wallet = MemberWallet::where('user_id', $userId)->lockForUpdate()->first();
            
            if (!$wallet || $wallet->locked_balance < $amount) {
                return; // Nothing to complete or already completed
            }

            // Complete the order: decrease locked_balance only
            $wallet->locked_balance -= $amount;
            $wallet->save();
        });
    }
}
