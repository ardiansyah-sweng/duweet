<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Enums\AccountType;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get IDs of expense accounts to attach transactions to
        $expenseAccounts = DB::table('financial_accounts')
            ->where('type', AccountType::EXPENSES->value)
            ->pluck('id')
            ->toArray();

        if (empty($expenseAccounts)) {
            $this->command->warn('No expense accounts found. Make sure FinancialAccounts are seeded first.');
            return;
        }

        // Sample transactions spanning last 3 months
        $sampleTransactions = [
            // Current month
            [
                'amount' => 500000, // Rp 500.000
                'description' => 'Bayar listrik bulan ini',
                'days_ago' => 5,
            ],
            [
                'amount' => 1500000, // Rp 1.500.000
                'description' => 'Bayar kost',
                'days_ago' => 7,
            ],
            // Last month
            [
                'amount' => 450000,
                'description' => 'Bayar listrik bulan lalu',
                'days_ago' => 35,
            ],
            [
                'amount' => 1500000,
                'description' => 'Bayar kost bulan lalu',
                'days_ago' => 37,
            ],
            // Two months ago
            [
                'amount' => 475000,
                'description' => 'Bayar listrik 2 bulan lalu',
                'days_ago' => 65,
            ],
            [
                'amount' => 1500000,
                'description' => 'Bayar kost 2 bulan lalu',
                'days_ago' => 67,
            ],
        ];

        foreach ($sampleTransactions as $transaction) {
            DB::table('transactions')->insert([
                'financial_account_id' => $expenseAccounts[array_rand($expenseAccounts)],
                'amount' => $transaction['amount'],
                'entry_type' => 'debit', // For expenses, we use debit
                'transaction_date' => Carbon::now()->subDays($transaction['days_ago']),
                'description' => $transaction['description'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $this->command->info('Sample transactions seeded: ' . count($sampleTransactions));
    }
}