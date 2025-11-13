<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * 
     * Seeder untuk membuat data transaksi dummy.
     * 
     * Prerequisites:
     * - AccountSeeder harus dijalankan terlebih dahulu (untuk financial accounts)
     * - Database harus sudah memiliki users dan user_accounts
     */
    public function run(): void
    {
        // Get table names from config
        $userAccountsTable = config('db_tables.user_account', 'user_accounts');
        $transactionsTable = config('db_tables.transaction', 'transactions');
        $financialAccountsTable = config('db_tables.financial_account', 'financial_accounts');

        // ===== VALIDATION: Check prerequisites =====
        $userAccountsCount = DB::table($userAccountsTable)->count();
        if ($userAccountsCount === 0) {
            throw new \Exception('No user accounts found! Please run UserSeeder and UserAccountSeeder first.');
        }

        $financialAccountsCount = DB::table($financialAccountsTable)
            ->where('is_group', false)
            ->count();

        if ($financialAccountsCount === 0) {
            throw new \Exception('No financial accounts found! Please run AccountSeeder first.');
        }

        // ===== GET USER ACCOUNTS =====
        $userAccounts = DB::table($userAccountsTable)
            ->select('id', 'id_user', 'username')
            ->limit(5)
            ->get();

        if ($userAccounts->isEmpty()) {
            throw new \Exception('No user accounts available for seeding!');
        }

        // ===== GET FINANCIAL ACCOUNTS =====
        $financialAccounts = DB::table($financialAccountsTable)
            ->where('is_group', false)
            ->limit(10)
            ->pluck('id')
            ->toArray();

        // ===== CREATE TRANSACTIONS =====
        // Transaction templates untuk masing-masing user account
        $transactionTemplates = [
            // Template 1: 5 transaksi
            [
                ['amount' => 500000, 'description' => 'Gaji Bulanan', 'entry_type' => 'debit', 'balance_effect' => 'increase'],
                ['amount' => 150000, 'description' => 'Belanja Bulanan', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 75000, 'description' => 'Transport', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 200000, 'description' => 'Bayar Listrik', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 300000, 'description' => 'Bonus Kinerja', 'entry_type' => 'debit', 'balance_effect' => 'increase'],
            ],
            // Template 2: 8 transaksi
            [
                ['amount' => 600000, 'description' => 'Gaji Bulanan', 'entry_type' => 'debit', 'balance_effect' => 'increase'],
                ['amount' => 100000, 'description' => 'Belanja Groceries', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 50000, 'description' => 'Makan Siang', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 250000, 'description' => 'Bayar Air', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 400000, 'description' => 'Freelance Project', 'entry_type' => 'debit', 'balance_effect' => 'increase'],
                ['amount' => 125000, 'description' => 'Bensin', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 80000, 'description' => 'Parkir Bulanan', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 175000, 'description' => 'Internet', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
            ],
            // Template 3: 3 transaksi
            [
                ['amount' => 750000, 'description' => 'Gaji Bulanan', 'entry_type' => 'debit', 'balance_effect' => 'increase'],
                ['amount' => 300000, 'description' => 'Belanja Elektronik', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 100000, 'description' => 'Makan di Restoran', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
            ],
            // Template 4: 10 transaksi
            [
                ['amount' => 550000, 'description' => 'Gaji Bulanan', 'entry_type' => 'debit', 'balance_effect' => 'increase'],
                ['amount' => 200000, 'description' => 'Belanja Pakaian', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 90000, 'description' => 'Kosmetik', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 150000, 'description' => 'Gym Membership', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 75000, 'description' => 'Kopi & Snack', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 300000, 'description' => 'Side Hustle', 'entry_type' => 'debit', 'balance_effect' => 'increase'],
                ['amount' => 180000, 'description' => 'Streaming Services', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 220000, 'description' => 'Belanja Online', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 95000, 'description' => 'Pulsa', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 125000, 'description' => 'Hadiah Ulang Tahun', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
            ],
            // Template 5: 6 transaksi
            [
                ['amount' => 650000, 'description' => 'Gaji Bulanan', 'entry_type' => 'debit', 'balance_effect' => 'increase'],
                ['amount' => 275000, 'description' => 'Belanja Bulanan', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 120000, 'description' => 'Bayar Token Listrik', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 85000, 'description' => 'Transport Online', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
                ['amount' => 350000, 'description' => 'Konsultasi', 'entry_type' => 'debit', 'balance_effect' => 'increase'],
                ['amount' => 165000, 'description' => 'Maintenance Motor', 'entry_type' => 'credit', 'balance_effect' => 'decrease'],
            ],
        ];

        $totalTransactions = 0;
        $userStats = [];

        foreach ($userAccounts as $index => $userAccount) {
            // Get template for this user (cycle through templates if more users than templates)
            $template = $transactionTemplates[$index % count($transactionTemplates)];

            foreach ($template as $transaction) {
                $transactionGroupId = Str::uuid()->toString();
                $financialAccountId = $financialAccounts[array_rand($financialAccounts)];

                DB::table($transactionsTable)->insert([
                    'transaction_group_id' => $transactionGroupId,
                    'user_account_id' => $userAccount->id,
                    'financial_account_id' => $financialAccountId,
                    'entry_type' => $transaction['entry_type'],
                    'amount' => $transaction['amount'],
                    'balance_effect' => $transaction['balance_effect'],
                    'description' => $transaction['description'],
                    'is_balance' => false,
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(1, 30)),
                ]);

                $totalTransactions++;
            }
        }
    }
}
