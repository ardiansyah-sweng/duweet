<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'              => 'Test User',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => Str::random(10),
                'usia'              => 21,
                'bulan_lahir'       => 8,
                'tanggal_lahir'     => 15,    // 1–31
                'tahun_lahir'       => 2002,  // smallint
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        $this->call([
            DemoDataSeeder::class,
            UserSeeder::class,
            UserAccountSeeder::class,
        ]);
    }
}