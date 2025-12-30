<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'User Januari',
            'email' => 'jan@example.com',
            'password' => Hash::make('password'),
            'created_at' => '2024-01-10',
        ]);

        User::create([
            'name' => 'User Februari',
            'email' => 'feb@example.com',
            'password' => Hash::make('password'),
            'created_at' => '2024-02-15',
        ]);

        User::create([
            'name' => 'User Maret',
            'email' => 'mar@example.com',
            'password' => Hash::make('password'),
            'created_at' => '2024-03-20',
        ]);
    }
}
