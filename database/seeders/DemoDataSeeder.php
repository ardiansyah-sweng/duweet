<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Asumsi Anda punya model User di app/Models/User.php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema; // <-- 1. TAMBAHKAN INI

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 2. NONAKTIFKAN FOREIGN KEY CHECKS
        Schema::disableForeignKeyConstraints();

        // Dapatkan nama tabel dari config
        $accountsTable = config('db_tables.financial_account', 'financial_accounts');
        $transactionsTable = config('db_tables.transactions', 'transactions');

        // Bersihkan data lama (sekarang aman karena foreign key dimatikan)
        DB::table($transactionsTable)->truncate();
        DB::table('user_accounts')->truncate(); 
        DB::table($accountsTable)->truncate();
        DB::table('users')->truncate();

        // ---- 1. Buat User ----
        $user = User::create([
            'name' => 'Demo User',
            'email' => 'demo@duweet.com',
            'password' => Hash::make('password'),
        ]);

        // ---- 2. Buat User Account (untuk login, sesuai PRD) ----
        $userAccount = DB::table('user_accounts')->insertGetId([
            'user_id' => $user->id,
            'username' => 'demouser',
            'email' => 'demo@duweet.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // ---- 3. Buat Financial Account (Tipe 'IN' - Income) ----
        $incomeAccount = DB::table($accountsTable)->insertGetId([
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

        // ---- 4. Buat Transaksi (Income) ----
        
        // Transaksi 1 (Bulan Ini)
        DB::table($transactionsTable)->insert([
            'transaction_group_id' => (string) Str::uuid(),
            'user_account_id' => $userAccount, 
            'financial_account_id' => $incomeAccount, 
            'entry_type' => 'credit', 
            'amount' => 5000000,
            'balance_effect' => 'increase',
            'description' => 'Gaji November',
            'is_balance' => true,
            'created_at' => now(), 
            'updated_at' => now(),
        ]);

        // Transaksi 2 (Bulan Lalu)
        DB::table($transactionsTable)->insert([
            'transaction_group_id' => (string) Str::uuid(),
            'user_account_id' => $userAccount,
            'financial_account_id' => $incomeAccount,
            'entry_type' => 'credit',
            'amount' => 2500000,
            'balance_effect' => 'increase',
            'description' => 'Bonus Proyek Oktober',
            'is_balance' => true,
            'created_at' => now()->subMonth(), 
            'updated_at' => now()->subMonth(),
        ]);
        
        // 3. AKTIFKAN KEMBALI FOREIGN KEY CHECKS
        Schema::enableForeignKeyConstraints();
    }
}

