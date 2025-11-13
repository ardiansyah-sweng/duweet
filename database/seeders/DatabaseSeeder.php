<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users with address fields required by users table migration
        User::create([
            'name' => 'Demo User',
            'first_name' => 'Demo',
            'last_name' => 'User',
            'email' => 'demo_full@duweet.com',
            'provinsi' => 'Jakarta',
            'kabupaten' => 'Jakarta Pusat',
            'kecamatan' => 'Menteng',
            'jalan' => 'Jl. Demo No. 1',
            'kode_pos' => '10310',
            'tanggal_lahir' => 1,
            'bulan_lahir' => 1,
            'tahun_lahir' => 1990,
            'usia' => 34,
        ]);

        User::create([
            'name' => 'Test User 2',
            'first_name' => 'Test',
            'last_name' => 'User Two',
            'email' => 'user2@example.com',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Bandung',
            'kecamatan' => 'Cicendo',
            'jalan' => 'Jl. Test No. 2',
            'kode_pos' => '40175',
            'tanggal_lahir' => 15,
            'bulan_lahir' => 6,
            'tahun_lahir' => 1992,
            'usia' => 32,
        ]);

        User::create([
            'name' => 'Test User 3',
            'first_name' => 'Test',
            'last_name' => 'User Three',
            'email' => 'user3@example.com',
            'provinsi' => 'Jawa Timur',
            'kabupaten' => 'Surabaya',
            'kecamatan' => 'Tegalsari',
            'jalan' => 'Jl. Test No. 3',
            'kode_pos' => '60123',
            'tanggal_lahir' => 28,
            'bulan_lahir' => 12,
            'tahun_lahir' => 1988,
            'usia' => 36,
        ]);

        // Run other seeders in order: user_accounts -> transactions
        $this->call([
            UserAccountSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}