<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate 10 dummy users sesuai factory & migration
        User::factory(10)->create();

        // ===============================
        // TAMBAHAN DATA UNTUK QUERY
        // COUNT USER PER TANGGAL & BULAN
        // ===============================

        User::create([
            'name' => 'User Januari',
            'email' => 'user_januari@example.com',
            'password' => Hash::make('password'),
            'created_at' => '2024-01-10',
            'updated_at' => now(),
        ]);

        User::create([
            'name' => 'User Februari',
            'email' => 'user_februari@example.com',
            'password' => Hash::make('password'),
            'created_at' => '2024-02-12',
            'updated_at' => now(),
        ]);

        User::create([
            'name' => 'User Maret',
            'email' => 'user_maret@example.com',
            'password' => Hash::make('password'),
            'created_at' => '2024-03-15',
            'updated_at' => now(),
        ]);

        User::create([
            'name' => 'User April',
            'email' => 'user_april@example.com',
            'password' => Hash::make('password'),
            'created_at' => '2024-04-20',
            'updated_at' => now(),
        ]);
    }
}
