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
        // Create sample users first
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('jawa'),
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('sumatra'),
        ]);

        $user3 = User::create([
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'password' => bcrypt('kalimantan'),
        ]);

        // Create user accounts
        UserAccount::create([
            UserAccountColumns::ID_USER => $user1->id,
            UserAccountColumns::USERNAME => 'johndoe',
            UserAccountColumns::EMAIL => 'johndoe@duweet.com',
            UserAccountColumns::PASSWORD => bcrypt('password123'),
            UserAccountColumns::VERIFIED_AT => now(),
            UserAccountColumns::IS_ACTIVE => true,
        ]);

        UserAccount::create([
            UserAccountColumns::ID_USER => $user2->id,
            UserAccountColumns::USERNAME => 'janesmith',
            UserAccountColumns::EMAIL => 'janesmith@duweet.com',
            UserAccountColumns::PASSWORD => bcrypt('skibidi'),
            UserAccountColumns::VERIFIED_AT => now(),
            UserAccountColumns::IS_ACTIVE => true,
        ]);

        UserAccount::create([
            UserAccountColumns::ID_USER => $user3->id,
            UserAccountColumns::USERNAME => 'bobjohnson',
            UserAccountColumns::EMAIL => 'bobjohnson@duweet.com',
            UserAccountColumns::PASSWORD => bcrypt('mewing'),
            UserAccountColumns::VERIFIED_AT => null, // Not verified yet
            UserAccountColumns::IS_ACTIVE => false, // Inactive account
        ]);

        UserAccount::create([
            UserAccountColumns::ID_USER => $user1->id,
            UserAccountColumns::USERNAME => 'johndoe_alt',
            UserAccountColumns::EMAIL => 'johndoe_alt@duweet.com',
            UserAccountColumns::PASSWORD => bcrypt('bombaclat'),
            UserAccountColumns::VERIFIED_AT => now(),
            UserAccountColumns::IS_ACTIVE => true,
        ]);

        $this->command->info('UserAccount seeder completed! Created 3 users and 4 user accounts.');
    }
}
