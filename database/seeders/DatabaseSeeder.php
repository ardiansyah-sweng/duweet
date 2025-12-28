<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\FinancialAccount;
use App\Models\Transaction;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // User
        $user = User::create([
            'name' => 'Mahasiswa',
            'email' => 'mahasiswa@example.com',
            'password' => bcrypt('password')
        ]);

<<<<<<< HEAD
        // User Account
        $userAccount = UserAccount::create([
            'user_id' => $user->id,
            'name' => 'Dompet Utama'
        ]);

        // Financial Account
        $financialAccount = FinancialAccount::create([
            'name' => 'Kas Utama',
            'type' => 'asset'
        ]);

        // Transaction
        Transaction::create([
            'user_account_id' => $userAccount->id,
            'financial_account_id' => $financialAccount->id,
            'transaction_group_id' => Str::uuid(),
            'entry_type' => 'debit',
            'balance_effect' => 'increase',
            'amount' => 500000,
            'description' => 'Saldo awal',
            'is_balance' => true
=======
        // Run seeders in order: users -> user_accounts -> financial_accounts -> transactions
        $this->call([
            FinancialAccountSeeder::class,
            UserSeeder::class,
            UserAccountSeeder::class,
            FinancialAccountSeeder::class,
<<<<<<< HEAD
=======
            // AccountSeeder::class,
            UserTelephoneSeeder::class,
            UserFinancialAccountSeeder::class,
>>>>>>> origin/main
            TransactionSeeder::class,
            AccountSeeder::class,
            TransaksiSeeder::class,
>>>>>>> main
        ]);
    }
}
