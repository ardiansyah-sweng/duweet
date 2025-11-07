<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;

class UserFinancialAccountSeeder extends Seeder
{
    /**
     * Seed data untuk testing query liquid assets
     */
    public function run(): void
    {
        // Pastikan ada users
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Creating sample users...');
            $users = collect([
                User::create([
                    'name' => 'Admin User',
                    'email' => 'admin@duweet.com',
                    'password' => bcrypt('password'),
                ]),
                User::create([
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'password' => bcrypt('password'),
                ]),
                User::create([
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com',
                    'password' => bcrypt('password'),
                ]),
            ]);
        }

        // Pastikan ada financial accounts dengan type AS (Asset)
        $financialAccounts = FinancialAccount::where('type', 'AS')->get();
        if ($financialAccounts->isEmpty()) {
            $this->command->warn('No Asset financial accounts found. Creating sample accounts...');
            $financialAccounts = collect([
                FinancialAccount::create([
                    'name' => 'Cash',
                    'type' => 'AS',
                    'balance' => 0,
                    'initial_balance' => 0,
                    'is_group' => false,
                    'is_active' => true,
                    'level' => 0,
                    'sort_order' => 1,
                ]),
                FinancialAccount::create([
                    'name' => 'Bank BCA',
                    'type' => 'AS',
                    'balance' => 0,
                    'initial_balance' => 0,
                    'is_group' => false,
                    'is_active' => true,
                    'level' => 0,
                    'sort_order' => 2,
                ]),
                FinancialAccount::create([
                    'name' => 'E-Wallet GoPay',
                    'type' => 'AS',
                    'balance' => 0,
                    'initial_balance' => 0,
                    'is_group' => false,
                    'is_active' => true,
                    'level' => 0,
                    'sort_order' => 3,
                ]),
            ]);
        }

        // Hapus data lama jika ada
        DB::table('user_financial_accounts')->truncate();

        // Seed user_financial_accounts dengan data sample
        $data = [];
        
        foreach ($users as $index => $user) {
            foreach ($financialAccounts->take(2) as $accountIndex => $account) {
                $initialBalance = ($index + 1) * ($accountIndex + 1) * 1000000; // 1jt, 2jt, dst
                $currentBalance = $initialBalance + (rand(100000, 500000)); // Tambah random

                $data[] = [
                    'user_id' => $user->id,
                    'financial_account_id' => $account->id,
                    'initial_balance' => $initialBalance,
                    'balance' => $currentBalance,
                    'is_active' => true,
                ];
            }
        }

        // Insert data
        DB::table('user_financial_accounts')->insert($data);

        $totalRecords = count($data);
        $totalBalance = array_sum(array_column($data, 'balance'));

        $this->command->info("✓ Seeded {$totalRecords} user financial account records");
        $this->command->info("✓ Total Balance: Rp " . number_format($totalBalance, 0, ',', '.'));
    }
}
