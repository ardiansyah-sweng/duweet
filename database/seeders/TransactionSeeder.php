<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Constants\TransactionColumns;

class TransactionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * 
     * Creates balanced transaction pairs following double-entry bookkeeping principles.
     */
    public function run(): void
    {
        $userAccountsTable = config('db_tables.user_account', 'user_accounts');
        $transactionsTable = config('db_tables.transaction', 'transactions');
        $financialAccountsTable = config('db_tables.financial_account', 'financial_accounts');

        // ===== VALIDATION =====
        if (DB::table($userAccountsTable)->count() === 0) {
            throw new \Exception('No user accounts found! Please run UserSeeder and UserAccountSeeder first.');
        }

        if (DB::table($financialAccountsTable)->where('is_group', false)->count() === 0) {
            throw new \Exception('No financial accounts found! Please run FinancialAccountSeeder first.');
        }

        // ===== GET USER ACCOUNTS =====
        $userAccounts = DB::table($userAccountsTable)
            ->select('id', 'id_user', 'username')
            ->get();

        // ===== GET FINANCIAL ACCOUNTS BY TYPE =====
        $financialAccounts = DB::table($financialAccountsTable)
            ->where('is_group', false)
            ->select('id', 'name', 'type')
            ->get()
            ->groupBy('type');

        $assetAccounts = $financialAccounts->get('AS', collect())->pluck('id')->toArray();
        $incomeAccounts = $financialAccounts->get('IN', collect())->pluck('id')->toArray();
        $expenseAccounts = $financialAccounts->get('EX', collect())->pluck('id')->toArray();
        $spendingAccounts = $financialAccounts->get('SP', collect())->pluck('id')->toArray();

        if (empty($assetAccounts)) {
            throw new \Exception('No asset accounts found! Need at least one asset account.');
        }

        // ===== TRANSACTION SCENARIOS =====
        $transactionScenarios = [
            [
                ['type' => 'income', 'amount' => 5000000, 'desc' => 'Gaji Bulanan', 'debit' => 'asset', 'credit' => 'income'],
                ['type' => 'expense', 'amount' => 1500000, 'desc' => 'Belanja Bulanan', 'debit' => 'expense', 'credit' => 'asset'],
            ],
            // Tambahkan scenario lain sesuai kebutuhan
        ];

        // ===== CREATE BALANCED TRANSACTIONS =====
        $factory = Transaction::factory();
        $allTransactions = [];

        foreach ($userAccounts as $index => $userAccount) {
            $scenario = $transactionScenarios[$index % count($transactionScenarios)];

            foreach ($scenario as $tx) {
                // Tentukan debit & credit accounts
                $debitAccountId = match($tx['debit']) {
                    'asset' => $assetAccounts[array_rand($assetAccounts)],
                    'expense' => $expenseAccounts[array_rand($expenseAccounts)],
                    'spending' => $spendingAccounts[array_rand($spendingAccounts)],
                    default => null
                };

                $creditAccountId = match($tx['credit']) {
                    'asset' => $assetAccounts[array_rand($assetAccounts)],
                    'income' => $incomeAccounts[array_rand($incomeAccounts)],
                    default => null
                };

                if (!$debitAccountId || !$creditAccountId) continue;

                $pair = $factory->balancedPair(
                    $userAccount->id,
                    $debitAccountId,
                    $creditAccountId,
                    $tx['amount'],
                    $tx['desc']
                );

                $allTransactions[] = $pair[0];
                $allTransactions[] = $pair[1];
            }
        }

        // ===== INSERT ALL TRANSACTIONS =====
        foreach (array_chunk($allTransactions, 100) as $chunk) {
            DB::table($transactionsTable)->insert($chunk);
        }
    }
}