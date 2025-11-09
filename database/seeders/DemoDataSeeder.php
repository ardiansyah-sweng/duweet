<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema; 

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil nama tabel dari konfigurasi
        $accountsTable = config('db_tables.financial_account', 'financial_accounts');
        $transactionsTable = 'transactions'; // Ganti dari config jika bug persists, tapi kita pakai default dulu
        $userAccountsTable = config('db_tables.user_account', 'user_accounts'); 
        
        // 🚨 SOLUSI MUTLAK: Menonaktifkan perintah DELETE yang menyebabkan bug timing
        Schema::disableForeignKeyConstraints();
        DB::reconnect(); 
        
        // 🔑 BARIS DELETE DIKOMENTARI UNTUK MENGHINDARI BUG TIMING PADA INSERT/DELETE
        // DB::statement("DELETE FROM `{$transactionsTable}`"); 
        // DB::statement("DELETE FROM `{$userAccountsTable}`");
        // DB::statement("DELETE FROM `{$accountsTable}`");
        // DB::statement("DELETE FROM `users`"); 

        // ---- 1. Buat User & User Account ----
        $user = User::create([
            'name' => 'Demo User Lengkap',
            'email' => 'demo_full@duweet.com', 
            'password' => Hash::make('password'), 
        ]);

        // Insert ke tabel user_accounts
        $userAccount = DB::table($userAccountsTable)->insertGetId([
            'id_user' => $user->id, 
            'username' => 'demofull',
            'email' => 'demo_full@duweet.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'verified_at' => now(), 
        ]);

        // ---- 2. Buat Financial Accounts ----
        $incomeAccount = DB::table($accountsTable)->insertGetId([
            'name' => 'Gaji Bulanan', 'type' => 'IN', 'balance' => 0, 'initial_balance' => 0, 'is_group' => false, 'is_active' => true, 'level' => 0, 'created_at' => now(), 'updated_at' => now(),
        ]);
        
        $expenseAccountRent = DB::table($accountsTable)->insertGetId([
            'name' => 'Biaya Sewa / Cicilan', 'type' => 'EX', 'balance' => 0, 'initial_balance' => 0, 'is_group' => false, 'is_active' => true, 'level' => 0, 'created_at' => now(), 'updated_at' => now(),
        ]);

        // ---- 3. Buat Transaksi (Januari - Desember 2025) ----
        
        $startDate = Carbon::create(2025, 1, 1);
        $endDate = Carbon::create(2025, 12, 1); 
        $currentDate = $startDate->copy();
        
        $baseIncome = 8000000;
        $baseRent = 2000000;

        while ($currentDate->lte($endDate)) {
            
            $month = $currentDate->month;
            $transactionDate = $currentDate->copy()->day(5);

            // 1. PENDAPATAN
            $incomeAmount = $baseIncome;
            if ($month == 5) { // Bonus di bulan Mei
                 $incomeAmount += 5000000;
            }

            DB::table($transactionsTable)->insert([
                'transaction_group_id' => (string) Str::uuid(), 
                'user_account_id' => $userAccount, 
                'financial_account_id' => $incomeAccount, 
                'entry_type' => 'credit', 
                'amount' => $incomeAmount, 
                'balance_effect' => 'increase', 
                'description' => 'Gaji Bulanan ' . $transactionDate->format('M Y'), 
                'is_balance' => true, 
                'created_at' => $transactionDate, 
                'updated_at' => $transactionDate,
            ]);


            // 2. PENGELUARAN
            DB::table($transactionsTable)->insert([
                'transaction_group_id' => (string) Str::uuid(), 
                'user_account_id' => $userAccount, 
                'financial_account_id' => $expenseAccountRent, 
                'entry_type' => 'debit', 
                'amount' => $baseRent, 
                'balance_effect' => 'decrease', 
                'description' => 'Biaya Sewa Bulanan ' . $transactionDate->format('M Y'), 
                'is_balance' => true, 
                'created_at' => $currentDate->copy()->day(1), 
                'updated_at' => $currentDate->copy()->day(1),
            ]);


            $currentDate->addMonth();
        }
        
        Schema::enableForeignKeyConstraints();
    }
}