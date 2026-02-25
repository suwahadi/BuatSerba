<?php

namespace Database\Seeders;

use App\Models\MemberWallet;
use App\Models\MemberBalanceLedger;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberWalletDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Get some existing users
        $users = User::where('role', 'regular')->limit(3)->get();

        if ($users->isEmpty()) {
            // If no regular users exist, get any users
            $users = User::limit(3)->get();
        }

        if ($users->isEmpty()) {
            $this->command->error('No users found in database. Please create some users first.');
            return;
        }

        // Create member wallets with demo balances
        foreach ($users as $index => $user) {
            $wallet = MemberWallet::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => ($index + 1) * 100000, // 100k, 200k, etc.
                    'locked_balance' => ($index + 1) * 25000, // 25k, 50k, etc.
                ]
            );

            // Create some demo ledger entries
            MemberBalanceLedger::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'source_type' => 'admin_credit',
                'source_id' => 1,
                'amount' => ($index + 1) * 100000,
                'balance_before' => 0,
                'balance_after' => ($index + 1) * 100000,
                'description' => 'Saldo awal demo',
                'reference_code' => 'DEMO-' . strtoupper(uniqid()),
            ]);

            // Create some debit entries for demo
            if ($index === 0) {
                MemberBalanceLedger::create([
                    'user_id' => $user->id,
                    'type' => 'debit',
                    'source_type' => 'order_payment',
                    'source_id' => 1,
                    'amount' => 50000,
                    'balance_before' => ($index + 1) * 100000,
                    'balance_after' => (($index + 1) * 100000) - 50000,
                    'description' => 'Pembayaran order #ORD-12345',
                    'reference_code' => 'ORDER-' . $user->id . '-12345',
                ]);

                // Lock some balance for demo
                MemberBalanceLedger::create([
                    'user_id' => $user->id,
                    'type' => 'debit',
                    'source_type' => 'order_payment',
                    'source_id' => 2,
                    'amount' => 25000,
                    'balance_before' => (($index + 1) * 100000) - 50000,
                    'balance_after' => (($index + 1) * 100000) - 75000,
                    'description' => 'Lock saldo untuk order #ORD-12346',
                    'reference_code' => 'ORDER-' . $user->id . '-12346',
                ]);
            }
        }

        // Create demo vouchers with cashback
        $vouchers = [
            [
                'voucher_name' => 'Voucher Cashback 10%',
                'voucher_code' => 'CASHBACK10',
                'type' => 'percentage',
                'amount' => 10,
                'cashback_type' => 'member_balance',
                'cashback_percentage' => 5,
                'min_spend' => 50000,
                'is_active' => true,
            ],
            [
                'voucher_name' => 'Voucher Cashback Rp 25.000',
                'voucher_code' => 'CASHBACK25K',
                'type' => 'fixed',
                'amount' => 20000,
                'cashback_type' => 'member_balance',
                'cashback_amount' => 25000,
                'min_spend' => 100000,
                'is_active' => true,
            ],
            [
                'voucher_name' => 'Voucher Regular (No Cashback)',
                'voucher_code' => 'REGULAR20',
                'type' => 'percentage',
                'amount' => 20,
                'cashback_type' => 'none',
                'cashback_amount' => 0,
                'cashback_percentage' => 0,
                'min_spend' => 75000,
                'is_active' => true,
            ],
        ];

        foreach ($vouchers as $voucherData) {
            Voucher::updateOrCreate(
                ['voucher_code' => $voucherData['voucher_code']],
                $voucherData
            );
        }

        $this->command->info('Member wallet demo data seeded successfully!');
        $this->command->info('Demo users:');
        $users->each(function ($user) {
            $wallet = $user->wallet;
            $this->command->info("- {$user->email} | Balance: Rp " . number_format($wallet->balance, 0, ',', '.'));
        });
        $this->command->info('Demo vouchers:');
        foreach ($vouchers as $voucher) {
            $this->command->info("- {$voucher['voucher_code']}: {$voucher['voucher_name']}");
        }
    }
}
