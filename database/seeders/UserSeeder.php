<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Muhammad Abyan',
                'email' => 'abyan06@gmail.com',
                'password' => Hash::make('informatika123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Rizky Maulana',
                'email' => 'rizky@gmail.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Salsabila Putri',
                'email' => 'salsa@gmail.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ahmad Fauzan',
                'email' => 'fauzan@gmail.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Nadia Aulia',
                'email' => 'nadia@gmail.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ],
        ]);
    }
}
