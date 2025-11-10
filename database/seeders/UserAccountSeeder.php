<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Constants\UserAccountColumns;

class UserAccountSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk tabel user_accounts.
     */
    public function run(): void
    {
        DB::table('user_accounts')->insert([
            UserAccountColumns::ID_USER      => 1, // Sesuai user pertama di UserSeeder
            UserAccountColumns::USERNAME     => 'abyan',
            UserAccountColumns::EMAIL        => 'abyan06@gmail.com',
            UserAccountColumns::PASSWORD     => Hash::make('informatika123'),
            UserAccountColumns::VERIFIED_AT  => now(),
            UserAccountColumns::IS_ACTIVE    => true,
        ]);
    }
}
