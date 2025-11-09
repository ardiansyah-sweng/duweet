<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SampleTotalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $driver = DB::connection()->getDriverName();

        // Disable foreign key checks for safe truncation
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // Truncate tables we will seed
        DB::table('user_account_totals')->truncate();
        DB::table('accounts')->truncate();
        DB::table('users')->truncate();

        // Re-enable foreign key checks
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Insert sample users (4 users)
        $users = [
            ['name' => 'Alice', 'email' => 'alice@example.test', 'password' => Hash::make('password')],
            ['name' => 'Bob', 'email' => 'bob@example.test', 'password' => Hash::make('password')],
            ['name' => 'Charlie', 'email' => 'charlie@example.test', 'password' => Hash::make('password')],
            ['name' => 'David', 'email' => 'david@example.test', 'password' => Hash::make('password')],
        ];

        $userIds = [];
        foreach ($users as $u) {
            $userIds[] = DB::table('users')->insertGetId(array_merge($u, ['created_at' => now(), 'updated_at' => now()]));
        }

        // Insert sample accounts (a set of accounts reused across users)
        $accounts = [
            ['name' => 'Cash', 'type' => 'asset', 'balance' => 1000, 'initial_balance' => 1000, 'is_group' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bank', 'type' => 'asset', 'balance' => 2000, 'initial_balance' => 2000, 'is_group' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Receivables', 'type' => 'asset', 'balance' => 500, 'initial_balance' => 500, 'is_group' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inventory', 'type' => 'asset', 'balance' => 300, 'initial_balance' => 300, 'is_group' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sales', 'type' => 'income', 'balance' => 150, 'initial_balance' => 150, 'is_group' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Expenses', 'type' => 'expense', 'balance' => 100, 'initial_balance' => 100, 'is_group' => false, 'created_at' => now(), 'updated_at' => now()],
        ];

        $accountIds = [];
        foreach ($accounts as $a) {
            $accountIds[] = DB::table('accounts')->insertGetId($a);
        }

        // Insert user_account_totals rows so each user has more than 1 account
        // Alice: Cash (1000) + Receivables (200) + Inventory (300) = 1500
        // Bob: Bank (2000) + Sales (150) = 2150
        // Charlie: Cash (500) + Expenses (100) = 600
        // David: Bank (800) + Receivables (50) + Sales (25) = 875
        DB::table('user_account_totals')->insert([
            // Alice
            ['user_id' => $userIds[0], 'account_id' => $accountIds[0], 'total_balance' => 1000, 'initial_balance' => 1000, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $userIds[0], 'account_id' => $accountIds[2], 'total_balance' => 200, 'initial_balance' => 200, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $userIds[0], 'account_id' => $accountIds[3], 'total_balance' => 300, 'initial_balance' => 300, 'created_at' => now(), 'updated_at' => now()],
            // Bob
            ['user_id' => $userIds[1], 'account_id' => $accountIds[1], 'total_balance' => 2000, 'initial_balance' => 2000, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $userIds[1], 'account_id' => $accountIds[4], 'total_balance' => 150, 'initial_balance' => 150, 'created_at' => now(), 'updated_at' => now()],
            // Charlie
            ['user_id' => $userIds[2], 'account_id' => $accountIds[0], 'total_balance' => 500, 'initial_balance' => 500, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $userIds[2], 'account_id' => $accountIds[5], 'total_balance' => 100, 'initial_balance' => 100, 'created_at' => now(), 'updated_at' => now()],
            // David
            ['user_id' => $userIds[3], 'account_id' => $accountIds[1], 'total_balance' => 800, 'initial_balance' => 800, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $userIds[3], 'account_id' => $accountIds[2], 'total_balance' => 50, 'initial_balance' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $userIds[3], 'account_id' => $accountIds[4], 'total_balance' => 25, 'initial_balance' => 25, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
