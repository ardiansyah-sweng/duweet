<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAccount;
use App\Constants\UserAccountColumns; // Wajib import ini

class UserAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==========================================
        // 1. BUAT DATA USER UTAMA (MANUAL)
        // ==========================================
        
        $user1 = User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'first_name' => 'John',
                'middle_name' => null,
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
                'middle_name' => null,
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
                'middle_name' => null,
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

        // ==========================================
        // 2. BUAT AKUN MANUAL (Login Credentials Tetap)
        // ==========================================

        // Akun Jane (Password: skibidi)
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

        // Akun Bob (Password: mewing)
        UserAccount::updateOrCreate(
            [UserAccountColumns::USERNAME => 'bobjohnson'],
            [
                UserAccountColumns::ID_USER => $user3->id,
                UserAccountColumns::EMAIL => 'bobjohnson@duweet.com',
                UserAccountColumns::PASSWORD => bcrypt('mewing'),
                UserAccountColumns::VERIFIED_AT => now(),
                UserAccountColumns::IS_ACTIVE => false,
            ]
        );

        // Akun John Alt (Password: bombaclat)
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

        // ==========================================
        // 3. GENERATE AKUN TAMBAHAN VIA FACTORY
        //    (Logic dari Incoming Change / Teman)
        // ==========================================

        // Loop ke semua user yang ada (termasuk John, Jane, Bob)
        User::all()->each(function (User $user) {
            $count = rand(1, 3);

            $willCopyEmail = (bool) rand(0, 1);
            $copyIndex = $willCopyEmail ? rand(0, $count - 1) : null;

            for ($i = 0; $i < $count; $i++) {
                // Inisialisasi factory untuk user ini
                $factory = UserAccount::factory()->for($user, 'user');

                // Logic copy email (menggunakan method di Factory kamu)
                if ($copyIndex !== null && $i === $copyIndex) {
                    $factory = $factory->useUserEmail($user->email);
                }

                // Eksekusi create
                $factory->create();
            }
        });

        $this->command->info('UserAccount seeder completed (Manual + Factory merged)!');
    }
}