<?php

namespace Database\Seeders;

use App\Constants\UserAccountColumns;
use App\Models\User;
use App\Models\UserAccount;
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
            [
                'name' => 'John Doe',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'provinsi' => 'DKI Jakarta',
                'kabupaten' => 'Jakarta Selatan',
                'kecamatan' => 'Kebayoran Baru',
                'jalan' => 'Jl. Sudirman No. 123',
                'kode_pos' => '12190',
                'tanggal_lahir' => 15,
                'bulan_lahir' => 6,
                'tahun_lahir' => 1990,
                'usia' => 35,
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'jane@example.com'],
            [
                'name' => 'Jane Smith',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'provinsi' => 'Jawa Barat',
                'kabupaten' => 'Bandung',
                'kecamatan' => 'Coblong',
                'jalan' => 'Jl. Dago No. 45',
                'kode_pos' => '40135',
                'tanggal_lahir' => 22,
                'bulan_lahir' => 3,
                'tahun_lahir' => 1992,
                'usia' => 33,
            ]
        );

        $user3 = User::firstOrCreate(
            ['email' => 'bob@example.com'],
            [
                'name' => 'Bob Johnson',
                'first_name' => 'Bob',
                'last_name' => 'Johnson',
                'provinsi' => 'Jawa Timur',
                'kabupaten' => 'Surabaya',
                'kecamatan' => 'Gubeng',
                'jalan' => 'Jl. Pemuda No. 67',
                'kode_pos' => '60271',
                'tanggal_lahir' => 8,
                'bulan_lahir' => 11,
                'tahun_lahir' => 1988,
                'usia' => 37,
            ]
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