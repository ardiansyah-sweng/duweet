<?php

namespace Database\Seeders;

use App\Constants\UserAccountColumns;
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
        // Create sample users first (idempotent)
        $user1 = User::firstOrCreate(
            ['email' => 'john@example.com'],
            ['name' => 'John Doe', 'password' => bcrypt('jawa')]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'jane@example.com'],
            ['name' => 'Jane Smith', 'password' => bcrypt('sumatra')]
        );

        $user3 = User::firstOrCreate(
            ['email' => 'bob@example.com'],
            ['name' => 'Bob Johnson', 'password' => bcrypt('kalimantan')]
        );

        // Create user accounts
        UserAccount::updateOrCreate(
            [UserAccountColumns::USERNAME => 'johndoe'],
            [
                UserAccountColumns::ID_USER => $user1->id,
                UserAccountColumns::EMAIL => 'johndoe@duweet.com',
                UserAccountColumns::PASSWORD => bcrypt('password123'),
                UserAccountColumns::VERIFIED_AT => now(),
                UserAccountColumns::IS_ACTIVE => true,
            ]
        );

        UserAccount::updateOrCreate(
            [UserAccountColumns::USERNAME => 'janesmith'],
            [
                UserAccountColumns::ID_USER => $user2->id,
                UserAccountColumns::EMAIL => 'janesmith@duweet.com',
                UserAccountColumns::PASSWORD => bcrypt('skibidi'),
                UserAccountColumns::VERIFIED_AT => now(),
                UserAccountColumns::IS_ACTIVE => true,
            ]
        );

        UserAccount::updateOrCreate(
            [UserAccountColumns::USERNAME => 'bobjohnson'],
            [
                UserAccountColumns::ID_USER => $user3->id,
                UserAccountColumns::EMAIL => 'bobjohnson@duweet.com',
                UserAccountColumns::PASSWORD => bcrypt('mewing'),
                UserAccountColumns::VERIFIED_AT => null, // Not verified yet
                UserAccountColumns::IS_ACTIVE => false, // Inactive account
            ]
        );

        UserAccount::updateOrCreate(
            [UserAccountColumns::USERNAME => 'johndoe_alt'],
            [
                UserAccountColumns::ID_USER => $user1->id,
                UserAccountColumns::EMAIL => 'johndoe_alt@duweet.com',
                UserAccountColumns::PASSWORD => bcrypt('bombaclat'),
                UserAccountColumns::VERIFIED_AT => now(),
                UserAccountColumns::IS_ACTIVE => true,
            ]
        );

        $this->command->info('UserAccount seeder completed! Created 3 users and 4 user accounts.');
    }
}
