<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
<<<<<<< HEAD
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
=======
use App\Models\User;
>>>>>>> 1ddf2b86ee702e9d70eeccf8ccd250a7abec4494

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
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
=======
        // Create a test user only if not already exists
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'email' => 'test@example.com',
                'name' => 'Test User',
            ]);
        }
>>>>>>> 1ddf2b86ee702e9d70eeccf8ccd250a7abec4494

        $this->call([
            UserSeeder::class,
            UserAccountSeeder::class,
<<<<<<< HEAD
            // AccountSeeder::class,
=======
            //AccountSeeder::class,
            FinancialAccountSeeder::class,
>>>>>>> 1ddf2b86ee702e9d70eeccf8ccd250a7abec4494
        ]);
    }
}