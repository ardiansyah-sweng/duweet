<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * 
     * Seeder untuk membuat data user dummy untuk testing.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Ahmad Santoso',
                'first_name' => 'Ahmad',
                'last_name' => 'Santoso',
                'email' => 'ahmad.santoso@example.com',
                'tanggal_lahir' => 15,
                'bulan_lahir' => 5,
                'tahun_lahir' => 1990,
                'usia' => 35,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'first_name' => 'Siti',
                'last_name' => 'Nurhaliza',
                'email' => 'siti.nurhaliza@example.com',
                'tanggal_lahir' => 20,
                'bulan_lahir' => 8,
                'tahun_lahir' => 1995,
                'usia' => 30,
            ],
            [
                'name' => 'Budi Prasetyo',
                'first_name' => 'Budi',
                'last_name' => 'Prasetyo',
                'email' => 'budi.prasetyo@example.com',
                'tanggal_lahir' => 10,
                'bulan_lahir' => 12,
                'tahun_lahir' => 1988,
                'usia' => 36,
            ],
            [
                'name' => 'Dewi Lestari',
                'first_name' => 'Dewi',
                'last_name' => 'Lestari',
                'email' => 'dewi.lestari@example.com',
                'tanggal_lahir' => 25,
                'bulan_lahir' => 3,
                'tahun_lahir' => 1992,
                'usia' => 33,
            ],
            [
                'name' => 'Eko Wijaya',
                'first_name' => 'Eko',
                'last_name' => 'Wijaya',
                'email' => 'eko.wijaya@example.com',
                'tanggal_lahir' => 5,
                'bulan_lahir' => 7,
                'tahun_lahir' => 1993,
                'usia' => 32,
            ],
        ];

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($users as $userData) {
            // Check if user already exists
            $existingUser = User::where('email', $userData['email'])->first();
            
            if ($existingUser) {
                $skippedCount++;
            } else {
                User::create($userData);
                $createdCount++;
            }
        }
    }
}
