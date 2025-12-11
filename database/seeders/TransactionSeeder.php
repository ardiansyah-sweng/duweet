<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use App\Constants\TransactionColumns;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactionsTable = config('db_tables.transaction', 'transactions');
        $accountsTable = config('db_tables.financial_account', 'financial_accounts');
        $userAccountsTable = config('db_tables.user_account', 'user_accounts');

        Schema::disableForeignKeyConstraints();

        // Ensure at least one income and one expense account exist
        $incomeAccount = DB::table($accountsTable)->where('type', 'IN')->first();
        if (! $incomeAccount) {
            $incomeId = DB::table($accountsTable)->insertGetId([
                'name' => 'Gaji Bulanan',
                'type' => 'IN',
                'balance' => 0,
                'initial_balance' => 0,
                'is_group' => false,
                'is_active' => true,
                'level' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $incomeId = $incomeAccount->id;
        }

        $expenseAccount = DB::table($accountsTable)->where('type', 'EX')->first();
        if (! $expenseAccount) {
            $expenseId = DB::table($accountsTable)->insertGetId([
                'name' => 'Biaya Sewa / Cicilan',
                'type' => 'EX',
                'balance' => 0,
                'initial_balance' => 0,
                'is_group' => false,
                'is_active' => true,
                'level' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $expenseId = $expenseAccount->id;
        }

        // Build transactions for each user_account (Jan-Dec 2025)
        $start = Carbon::create(2025, 1, 1);
        $end = Carbon::create(2025, 12, 1);

        $userAccounts = DB::table($userAccountsTable)->get();

        foreach ($userAccounts as $ua) {
            $current = $start->copy();
            while ($current->lte($end)) {
                $month = $current->month;
                $txDate = $current->copy()->day(5);

                // income
                DB::table($transactionsTable)->insert([
                    TransactionColumns::TRANSACTION_GROUP_ID => (string) Str::uuid(),
                    TransactionColumns::USER_ACCOUNT_ID => $ua->id,
                    TransactionColumns::FINANCIAL_ACCOUNT_ID => $incomeId,
                    TransactionColumns::ENTRY_TYPE => 'credit',
                    TransactionColumns::AMOUNT => 8000000 + ($month === 5 ? 5000000 : 0),
                    TransactionColumns::BALANCE_EFFECT => 'increase',
                    TransactionColumns::DESCRIPTION => 'Gaji Bulanan ' . $txDate->format('M Y'),
                    TransactionColumns::IS_BALANCE => true,
                    TransactionColumns::CREATED_AT => $txDate,
                    TransactionColumns::UPDATED_AT => $txDate,
                ]);

                // expense
                DB::table($transactionsTable)->insert([
                    TransactionColumns::TRANSACTION_GROUP_ID => (string) Str::uuid(),
                    TransactionColumns::USER_ACCOUNT_ID => $ua->id,
                    TransactionColumns::FINANCIAL_ACCOUNT_ID => $expenseId,
                    TransactionColumns::ENTRY_TYPE => 'debit',
                    TransactionColumns::AMOUNT => 2000000,
                    TransactionColumns::BALANCE_EFFECT => 'decrease',
                    TransactionColumns::DESCRIPTION => 'Biaya Sewa Bulanan ' . $txDate->format('M Y'),
                    TransactionColumns::IS_BALANCE => true,
                    TransactionColumns::CREATED_AT => $current->copy()->day(1),
                    TransactionColumns::UPDATED_AT => $current->copy()->day(1),
                ]);

                $current->addMonth();
            }
        }

        Schema::enableForeignKeyConstraints();

        // Tambahan: Membuat beberapa data transaksi random menggunakan TransactionFactory
        // untuk variasi data selain gaji dan sewa bulanan
        Transaction::factory(30)->create();
    }
}
