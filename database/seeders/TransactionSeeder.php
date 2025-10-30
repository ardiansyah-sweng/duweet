<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use Faker\Factory as Faker;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks for truncation (works for sqlite & mysql)
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

    // Resolve table names from config to avoid hard-coded table names
    $transactionsTable = config('db_tables.transaction', 'transactions');
    // Use the canonical financial_account config key and a sane default
    $accountsTable = config('db_tables.financial_account', 'financial_accounts');

    // Truncate transactions table to start fresh for testing
    DB::table($transactionsTable)->truncate();

    $users = User::all();
        if ($users->isEmpty()) {
            // If no users exist, create a small set so seeder can run standalone
            User::factory(5)->create();
            $users = User::all();
        }

        // Pick leaf accounts (is_group = false) so transactions attach to real accounts
    $accountIds = DB::table($accountsTable)->where('is_group', false)->pluck('id')->toArray();
        if (empty($accountIds)) {
            // nothing to do if there are no accounts
            return;
        }

        $faker = Faker::create();

        foreach ($users as $user) {
            // random transactions per user to create realistic distribution
            $txCount = rand(15, 35);

            for ($i = 0; $i < $txCount; $i++) {
                $accountId = $faker->randomElement($accountIds);
                // entry_type debit/credit
                $entryType = $faker->randomElement(['debit', 'credit']);
                // amount in smallest currency unit (e.g., cents) or integer
                $amount = $faker->numberBetween(1000, 5000000);
                // simple balance effect mapping (this may vary by account type)
                $balanceEffect = $entryType === 'debit' ? 'decrease' : 'increase';

                DB::table($transactionsTable)->insert([
                    'transaction_group_id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'account_id' => $accountId,
                    'entry_type' => $entryType,
                    'amount' => $amount,
                    'balance_effect' => $balanceEffect,
                    'description' => $faker->sentence(6),
                    'is_balance' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Re-enable foreign key checks
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
