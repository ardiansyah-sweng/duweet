<?php

namespace Database\Seeders;

// ...existing code...
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ensure a known test user exists
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'              => 'Test User',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => Str::random(10),
                'usia'              => 21,
                'bulan_lahir'       => 8,
                'tanggal_lahir'     => '2002-08-15',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );
        // Create a test user only if not already exists
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'email' => 'test@example.com',
                'name' => 'Test User',
            ]);
        }

        // Run seeders in order: users -> user_accounts -> financial_accounts -> transactions
        $this->call([
            UserSeeder::class,
            UserAccountSeeder::class,
            // AccountSeeder::class,
            //AccountSeeder::class,
            FinancialAccountSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}