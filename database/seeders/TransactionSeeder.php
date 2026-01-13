<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Constants\TransactionColumns;

class TransactionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * 
     * Creates balanced transaction pairs following double-entry bookkeeping principles.
     * Each transaction has equal debit and credit entries with the same transaction_group_id.
     * 
     * Prerequisites:
     * - FinancialAccountSeeder must be run first
     * - UserSeeder and UserAccountSeeder must be run first
     */
    public function run(): void
    {
        $userAccountsTable = config('db_tables.user_account');
        $transactionsTable = config('db_tables.transaction');
        $financialAccountsTable = config('db_tables.financial_account');

        $userAccountsCount = DB::table($userAccountsTable)->count();
        if ($userAccountsCount === 0) {
            throw new \Exception('No user accounts found! Please run UserSeeder and UserAccountSeeder first.');
        }

        $financialAccountsCount = DB::table($financialAccountsTable)
            ->where('is_group', false)
            ->count();

        if ($financialAccountsCount === 0) {
            throw new \Exception('No financial accounts found! Please run FinancialAccountSeeder first.');
        }

        $userAccounts = DB::table($userAccountsTable)
            ->select('id', 'id_user', 'username')
            ->get();

        if ($userAccounts->isEmpty()) {
            throw new \Exception('No user accounts available for seeding!');
        }

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

        $transactionScenarios = [
            [
                ['type' => 'income', 'amount' => 5000000, 'desc' => 'Gaji Bulanan', 'debit' => 'asset', 'credit' => 'income'],
                ['type' => 'expense', 'amount' => 1500000, 'desc' => 'Belanja Bulanan', 'debit' => 'expense', 'credit' => 'asset'],
                ['type' => 'expense', 'amount' => 750000, 'desc' => 'Bayar Listrik & Air', 'debit' => 'expense', 'credit' => 'asset'],
                ['type' => 'spending', 'amount' => 500000, 'desc' => 'Jajan & Hiburan', 'debit' => 'spending', 'credit' => 'asset'],
                ['type' => 'expense', 'amount' => 300000, 'desc' => 'Transport & Bensin', 'debit' => 'expense', 'credit' => 'asset'],
            ],
            [
                ['type' => 'income', 'amount' => 3500000, 'desc' => 'Gaji Pokok', 'debit' => 'asset', 'credit' => 'income'],
                ['type' => 'income', 'amount' => 2000000, 'desc' => 'Project Freelance', 'debit' => 'asset', 'credit' => 'income'],
                ['type' => 'expense', 'amount' => 1200000, 'desc' => 'Sewa Kos/Kontrakan', 'debit' => 'expense', 'credit' => 'asset'],
                ['type' => 'expense', 'amount' => 800000, 'desc' => 'Belanja Groceries', 'debit' => 'expense', 'credit' => 'asset'],
                ['type' => 'spending', 'amount' => 400000, 'desc' => 'Nongkrong & Makan', 'debit' => 'spending', 'credit' => 'asset'],
                ['type' => 'expense', 'amount' => 250000, 'desc' => 'Internet & Pulsa', 'debit' => 'expense', 'credit' => 'asset'],
                ['type' => 'spending', 'amount' => 600000, 'desc' => 'Shopping Online', 'debit' => 'spending', 'credit' => 'asset'],
                ['type' => 'expense', 'amount' => 350000, 'desc' => 'Bayar Token Listrik', 'debit' => 'expense', 'credit' => 'asset'],
            ],
            [
                ['type' => 'income', 'amount' => 4000000, 'desc' => 'Gaji Bulanan', 'debit' => 'asset', 'credit' => 'income'],
                ['type' => 'expense', 'amount' => 2000000, 'desc' => 'Belanja & Kebutuhan', 'debit' => 'expense', 'credit' => 'asset'],
                ['type' => 'spending', 'amount' => 800000, 'desc' => 'Entertainment', 'debit' => 'spending', 'credit' => 'asset'],
            ],
            [
                ['type' => 'income', 'amount' => 6000000, 'desc' => 'Gaji & Bonus', 'debit' => 'asset', 'credit' => 'income'],
                ['type' => 'expense', 'amount' => 1800000, 'desc' => 'Sewa Apartemen', 'debit' => 'expense', 'credit' => 'asset'],
                ['type' => 'spending', 'amount' => 900000, 'desc' => 'Belanja Pakaian', 'debit' => 'spending', 'credit' => 'asset'],
                ['type' => 'spending', 'amount' => 700000, 'desc' => 'Gadget & Aksesoris', 'debit' => 'spending', 'credit' => 'asset'],
                ['type' => 'expense', 'amount' => 500000, 'desc' => 'Gym & Fitness', 'debit' => 'expense', 'credit' => 'asset'],
                ['type' => 'spending', 'amount' => 450000, 'desc' => 'Cafe & Restaurant', 'debit' => 'spending', 'credit' => 'asset'],
                ['type' => 'expense', 'amount' => 600000, 'desc' => 'Utilities (Listrik, Air, Gas)', 'debit' => 'expense', 'credit' => 'asset'],
                ['type' => 'spending', 'amount' => 350000, 'desc' => 'Streaming Services', 'debit' => 'spending', 'credit' => 'asset'],
                ['type' => 'expense', 'amount' => 400000, 'desc' => 'Transport & Parkir', 'debit' => 'expense', 'credit' => 'asset'],
                ['type' => 'spending', 'amount' => 550000, 'desc' => 'Hobi & Koleksi', 'debit' => 'spending', 'credit' => 'asset'],
            ],
            [
                ['type' => 'income', 'amount' => 5500000, 'desc' => 'Gaji Bulanan', 'debit' => 'asset', 'credit' => 'income'],
                ['type' => 'expense', 'amount' => 1500000, 'desc' => 'Bayar Cicilan Motor', 'debit' => 'expense', 'credit' => 'asset'],
                ['type' => 'expense', 'amount' => 1000000, 'desc' => 'Belanja Bulanan', 'debit' => 'expense', 'credit' => 'asset'],
                ['type' => 'expense', 'amount' => 400000, 'desc' => 'Bensin & Service', 'debit' => 'expense', 'credit' => 'asset'],
                ['type' => 'spending', 'amount' => 600000, 'desc' => 'Jalan-jalan Weekend', 'debit' => 'spending', 'credit' => 'asset'],
                ['type' => 'expense', 'amount' => 300000, 'desc' => 'Maintenance Rumah', 'debit' => 'expense', 'credit' => 'asset'],
            ],
        ];

        $factory = Transaction::factory();
        $allTransactions = [];

        foreach ($userAccounts as $index => $userAccount) {
            $scenario = $transactionScenarios[$index % count($transactionScenarios)];

            foreach ($scenario as $tx) {
                $debitAccountId = null;
                $creditAccountId = null;

                switch ($tx['debit']) {
                    case 'asset':
                        $debitAccountId = !empty($assetAccounts) ? $assetAccounts[array_rand($assetAccounts)] : null;
                        break;
                    case 'expense':
                        $debitAccountId = !empty($expenseAccounts) ? $expenseAccounts[array_rand($expenseAccounts)] : null;
                        break;
                    case 'spending':
                        $debitAccountId = !empty($spendingAccounts) ? $spendingAccounts[array_rand($spendingAccounts)] : null;
                        break;
                }

                switch ($tx['credit']) {
                    case 'asset':
                        $creditAccountId = !empty($assetAccounts) ? $assetAccounts[array_rand($assetAccounts)] : null;
                        break;
                    case 'income':
                        $creditAccountId = !empty($incomeAccounts) ? $incomeAccounts[array_rand($incomeAccounts)] : null;
                        break;
                }

                if (!$debitAccountId || !$creditAccountId) {
                    continue;
                }

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

        if (!empty($allTransactions)) {
            $chunks = array_chunk($allTransactions, 100);
            foreach ($chunks as $chunk) {
                DB::table($transactionsTable)->insert($chunk);
            }
        }

        Transaction::factory(15)->daysAgo(90)->create();
        Transaction::factory(10)->between('-1 year', '-6 months')->create();
        Transaction::factory(5)->today()->create();
    }
}

