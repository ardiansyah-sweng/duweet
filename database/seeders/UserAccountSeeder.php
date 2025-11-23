<?php

namespace Database\Seeders;

use App\Constants\UserAccountColumns;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Database\Seeder;

class UserAccountSeeder extends Seeder
{
        /**
         * Helper untuk memastikan tanggal valid (misal 31 Februari jadi 28 Februari)
         */
        private function safeDate($dateStr)
        {
            $dt = date_parse($dateStr);
            if (!checkdate($dt['month'], $dt['day'], $dt['year'])) {
                // fallback ke tanggal terakhir bulan tsb
                $lastDay = cal_days_in_month(CAL_GREGORIAN, $dt['month'], $dt['year']);
                return sprintf('%04d-%02d-%02d', $dt['year'], $dt['month'], $lastDay);
            }
            return $dateStr;
        }

    public function run(): void
    {
        // Create sample users first (idempotent)
        $user1 = User::firstOrCreate(
            ['email' => 'dimas@example.com'],
            [
                'name' => 'Dimas Pratama',
                'first_name' => 'Dimas',
                'last_name' => 'Pratama',
                'provinsi' => 'Sumatera Selatan',
                'kabupaten' => 'Prabumulih Barat',
                'kecamatan' => 'Mutang Tapus',
                'jalan' => 'Jl. Sudirman No. 123',
                'kode_pos' => '12190',
                'tanggal_lahir' => $this->safeDate('2006-01-31'),
                'bulan_lahir' => 1,
                'tahun_lahir' => 2006,
                'usia' => 19,
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'azam@example.com'],
            [
                'name' => 'Azzam Abdul',
                'first_name' => 'Azzam',
                'last_name' => 'Abdul',
                'provinsi' => 'Jawa Barat',
                'kabupaten' => 'Bekasi',
                'kecamatan' => 'Coblong',
                'jalan' => 'Jl. Dago No. 45',
                'kode_pos' => '40135',
                'tanggal_lahir' => $this->safeDate('2006-4-24'),
                'bulan_lahir' => 4,
                'tahun_lahir' => 2006,
                'usia' => 19,
            ]
        );

        $user3 = User::firstOrCreate(
            ['email' => 'abyan@example.com'],
            [
                'name' => 'Abyan Furina',
                'first_name' => 'Abyan',
                'last_name' => 'Furina',
                'provinsi' => 'Jawa Barat',
                'kabupaten' => 'Cirebon',
                'kecamatan' => 'Gubeng',
                'jalan' => 'Jl. Pemuda No. 67',
                'kode_pos' => '60271',
                'tanggal_lahir' => $this->safeDate('2006-02-31'),
                'bulan_lahir' => 02,
                'tahun_lahir' => 2006,
                'usia' => 19,
            ]
        );

        // Create user accounts
        UserAccount::updateOrCreate(
            [UserAccountColumns::USERNAME => 'dimas'],
            [
                UserAccountColumns::ID_USER => $user1->id,
                UserAccountColumns::EMAIL => 'dimas@duweet.com',
                UserAccountColumns::PASSWORD => bcrypt('password123'),
                UserAccountColumns::VERIFIED_AT => now(),
                UserAccountColumns::IS_ACTIVE => true,
            ]
        );

        UserAccount::updateOrCreate(
            [UserAccountColumns::USERNAME => 'azzam'],
            [
                UserAccountColumns::ID_USER => $user2->id,
                UserAccountColumns::EMAIL => 'azzam@duweet.com',
                UserAccountColumns::PASSWORD => bcrypt('akusukaroblox'),
                UserAccountColumns::VERIFIED_AT => now(),
                UserAccountColumns::IS_ACTIVE => true,
            ]
        );

        UserAccount::updateOrCreate(
            [UserAccountColumns::USERNAME => 'abyan'],
            [
                UserAccountColumns::ID_USER => $user3->id,
                UserAccountColumns::EMAIL => 'abyan@duweet.com',
                UserAccountColumns::PASSWORD => bcrypt('akusukafurina'),
                UserAccountColumns::VERIFIED_AT => null, // Not verified yet
                UserAccountColumns::IS_ACTIVE => false, // Inactive account
            ]
        );

        UserAccount::updateOrCreate(
            [UserAccountColumns::USERNAME => 'satya'],
            [
                UserAccountColumns::ID_USER => $user1->id,
                UserAccountColumns::EMAIL => 'satya@duweet.com',
                UserAccountColumns::PASSWORD => bcrypt('castorice'),
                UserAccountColumns::VERIFIED_AT => now(),
                UserAccountColumns::IS_ACTIVE => true,
            ]
        );

        $this->command->info('UserAccount seeder completed! Created 3 users and 4 user accounts.');
    }

}
