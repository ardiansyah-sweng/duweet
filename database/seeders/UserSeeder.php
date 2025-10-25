<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Buat 1 user utama
        $user = User::create([
            'name' => 'Muchsin',
            'first_name' => 'Muchsin',
            'middle_name' => null,
            'last_name' => 'Ahmad',
            'email' => 'muchsin@example.com',
            'tanggal_lahir' => 15,
            'bulan_lahir' => 5,
            'tahun_lahir' => 2000,
            'usia' => 25,
            'password' => Hash::make('password123'),
        ]);

        // 🔹 Buat akun login untuk user tersebut
        $account = UserAccount::create([
            'user_id' => $user->id,
            'username' => 'muchsin_acc',
            'email' => 'muchsin@example.com', // bisa sama dengan user.email
            'password' => Hash::make('secret'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // 🔹 Tambahkan transaksi awal (contoh: setoran pertama)
        Transaction::create([
            'transaction_group_id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'account_id' => $account->id,
            'entry_type' => 'debit',
            'amount' => 500000,
            'balance_effect' => 'increase',
            'description' => 'Initial deposit',
            'is_balance' => true,
        ]);

        // 🔹 Tambahkan transaksi kedua (contoh: pengeluaran)
        Transaction::create([
            'transaction_group_id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'account_id' => $account->id,
            'entry_type' => 'credit',
            'amount' => 150000,
            'balance_effect' => 'decrease',
            'description' => 'Purchase - App Subscription',
            'is_balance' => true,
        ]);
    }
}
