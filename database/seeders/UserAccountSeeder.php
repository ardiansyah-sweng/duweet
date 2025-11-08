<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAccountSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * 
     * Seeder untuk membuat user accounts untuk testing.
     * 
     * Prerequisites:
     * - UserSeeder harus dijalankan terlebih dahulu
     */
    public function run(): void
    {
        // Get table name from config
        $userAccountsTable = config('db_tables.user_account', 'user_accounts');

        // ===== VALIDATION: Check if users exist =====
        $usersCount = User::count();
        if ($usersCount === 0) {
            throw new \Exception('No users found! Please run UserSeeder first.');
        }

        // ===== GET USERS =====
        $users = User::whereIn('email', [
            'ahmad.santoso@example.com',
            'siti.nurhaliza@example.com',
            'budi.prasetyo@example.com',
            'dewi.lestari@example.com',
            'eko.wijaya@example.com',
        ])->get();

        if ($users->isEmpty()) {
            throw new \Exception('Test users not found! Please run UserSeeder first.');
        }

        // ===== CREATE USER ACCOUNTS =====
        $createdCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            // Check if user account already exists
            $existingAccount = DB::table($userAccountsTable)
                ->where('id_user', $user->id)
                ->first();

            if ($existingAccount) {
                $skippedCount++;
                continue;
            }

            // Create username from name
            $username = strtolower(str_replace(' ', '_', $user->name));

            // Check if username already taken
            $usernameExists = DB::table($userAccountsTable)
                ->where('username', $username)
                ->exists();

            if ($usernameExists) {
                // Add random number if username exists
                $username .= '_' . rand(100, 999);
            }

            // Insert user account
            DB::table($userAccountsTable)->insert([
                'id_user' => $user->id,
                'username' => $username,
                'email' => $user->email,
                'password' => Hash::make('password123'),
                'verified_at' => now(),
                'is_active' => true,
            ]);

            $createdCount++;
        }
    }
}
