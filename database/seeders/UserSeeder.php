<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;    
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan FK sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

         DB::table('users')->insert([
            [
                'name' => 'Muchsin Hidayat',
                'first_name' => 'Muchsin',
                'middle_name' => '',
                'last_name' => 'Hidayat',
                'email' => 'muchsin@example.com',
                'tanggal_lahir' => 12,
                'bulan_lahir' => 5,
                'tahun_lahir' => 2000,
                'usia' => 25,
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rina Putri',
                'first_name' => 'Rina',
                'middle_name' => '',
                'last_name' => 'Putri',
                'email' => 'rina@example.com',
                'tanggal_lahir' => 3,
                'bulan_lahir' => 7,
                'tahun_lahir' => 1998,
                'usia' => 27,
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Andi Saputra',
                'first_name' => 'Andi',
                'middle_name' => '',
                'last_name' => 'Saputra',
                'email' => 'andi@example.com',
                'tanggal_lahir' => 22,
                'bulan_lahir' => 11,
                'tahun_lahir' => 1995,
                'usia' => 30,
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dewi Lestari',
                'first_name' => 'Dewi',
                'middle_name' => '',
                'last_name' => 'Lestari',
                'email' => 'dewi@example.com',
                'tanggal_lahir' => 5,
                'bulan_lahir' => 2,
                'tahun_lahir' => 2001,
                'usia' => 24,
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Budi Santoso',
                'first_name' => 'Budi',
                'middle_name' => '',
                'last_name' => 'Santoso',
                'email' => 'budi@example.com',
                'tanggal_lahir' => 15,
                'bulan_lahir' => 8,
                'tahun_lahir' => 1990,
                'usia' => 35,
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
