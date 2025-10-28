<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample users first
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'),
        ]);

        $user3 = User::create([
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Create user accounts
        UserAccount::create([
            'user_id' => $user1->id,
            'username' => 'johndoe',
            'email' => 'johndoe@duweet.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        UserAccount::create([
            'user_id' => $user2->id,
            'username' => 'janesmith',
            'email' => 'janesmith@duweet.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        UserAccount::create([
            'user_id' => $user3->id,
            'username' => 'bobjohnson',
            'email' => 'bobjohnson@duweet.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => null, // Not verified yet
            'is_active' => false, // Inactive account
        ]);

        UserAccount::create([
            'user_id' => $user1->id,
            'username' => 'johndoe_alt',
            'email' => 'johndoe_alt@duweet.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->command->info('UserAccount seeder completed! Created 3 users and 4 user accounts.');
    }
}
