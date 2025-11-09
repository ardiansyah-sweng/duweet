<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserAccount;
use Illuminate\Support\Facades\DB;    
use Illuminate\Support\Facades\Hash;

class UserAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan foreign key sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        UserAccount::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ambil semua ID dari tabel users
        $userIds = DB::table('users')->pluck('id');

        $accounts = [];
        foreach ($userIds as $i => $id) {
            $accounts[] = [
                'id_user' => $id, // ✅ disesuaikan dengan constant & migration
                'username' => 'user_acc_' . ($i + 1),
                'email' => 'user_acc' . ($i + 1) . '@example.com',
                'password' => Hash::make('acc12345'),
                'verified_at' => now(), // ✅ disesuaikan juga
                'is_active' => true,
            ];
        }

        // Masukkan data ke tabel user_accounts
        DB::table('user_accounts')->insert($accounts);
    }
}
