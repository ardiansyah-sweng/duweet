<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Constants\UserColumns;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan Foreign Key sementara untuk reset data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $table = config('db_tables.user');

        DB::table($table)->insert([
            [
                UserColumns::NAME           => 'Muchsin Hidayat',
                UserColumns::FIRST_NAME     => 'Muchsin',
                UserColumns::MIDDLE_NAME    => null,
                UserColumns::LAST_NAME      => 'Hidayat',
                UserColumns::EMAIL          => 'muchsin@example.com',
                UserColumns::PROVINSI       => 'Jawa Timur',
                UserColumns::KABUPATEN      => 'Malang',
                UserColumns::KECAMATAN      => 'Lowokwaru',
                UserColumns::JALAN          => 'Jl. Soekarno Hatta No. 21',
                UserColumns::KODE_POS       => '65141',
                UserColumns::TANGGAL_LAHIR  => 12,
                UserColumns::BULAN_LAHIR    => 5,
                UserColumns::TAHUN_LAHIR    => 2000,
                UserColumns::USIA           => 25,
                'password'                  => Hash::make('password123'),
                'created_at'                => now(),
                'updated_at'                => now(),
            ],
            [
                UserColumns::NAME           => 'Rina Putri',
                UserColumns::FIRST_NAME     => 'Rina',
                UserColumns::MIDDLE_NAME    => null,
                UserColumns::LAST_NAME      => 'Putri',
                UserColumns::EMAIL          => 'rina@example.com',
                UserColumns::PROVINSI       => 'Jawa Barat',
                UserColumns::KABUPATEN      => 'Bandung',
                UserColumns::KECAMATAN      => 'Cicendo',
                UserColumns::JALAN          => 'Jl. Pasteur No. 88',
                UserColumns::KODE_POS       => '40171',
                UserColumns::TANGGAL_LAHIR  => 3,
                UserColumns::BULAN_LAHIR    => 7,
                UserColumns::TAHUN_LAHIR    => 1998,
                UserColumns::USIA           => 27,
                'password'                  => Hash::make('password123'),
                'created_at'                => now(),
                'updated_at'                => now(),
            ],
            [
                UserColumns::NAME           => 'Andi Saputra',
                UserColumns::FIRST_NAME     => 'Andi',
                UserColumns::MIDDLE_NAME    => null,
                UserColumns::LAST_NAME      => 'Saputra',
                UserColumns::EMAIL          => 'andi@example.com',
                UserColumns::PROVINSI       => 'DKI Jakarta',
                UserColumns::KABUPATEN      => 'Jakarta Selatan',
                UserColumns::KECAMATAN      => 'Kebayoran Baru',
                UserColumns::JALAN          => 'Jl. Senopati No. 10',
                UserColumns::KODE_POS       => '12110',
                UserColumns::TANGGAL_LAHIR  => 22,
                UserColumns::BULAN_LAHIR    => 11,
                UserColumns::TAHUN_LAHIR    => 1995,
                UserColumns::USIA           => 30,
                'password'                  => Hash::make('password123'),
                'created_at'                => now(),
                'updated_at'                => now(),
            ],
            [
                UserColumns::NAME           => 'Dewi Lestari',
                UserColumns::FIRST_NAME     => 'Dewi',
                UserColumns::MIDDLE_NAME    => null,
                UserColumns::LAST_NAME      => 'Lestari',
                UserColumns::EMAIL          => 'dewi@example.com',
                UserColumns::PROVINSI       => 'Jawa Tengah',
                UserColumns::KABUPATEN      => 'Semarang',
                UserColumns::KECAMATAN      => 'Banyumanik',
                UserColumns::JALAN          => 'Jl. Setiabudi No. 56',
                UserColumns::KODE_POS       => '50263',
                UserColumns::TANGGAL_LAHIR  => 5,
                UserColumns::BULAN_LAHIR    => 2,
                UserColumns::TAHUN_LAHIR    => 2001,
                UserColumns::USIA           => 24,
                'password'                  => Hash::make('password123'),
                'created_at'                => now(),
                'updated_at'                => now(),
            ],
            [
                UserColumns::NAME           => 'Budi Santoso',
                UserColumns::FIRST_NAME     => 'Budi',
                UserColumns::MIDDLE_NAME    => null,
                UserColumns::LAST_NAME      => 'Santoso',
                UserColumns::EMAIL          => 'budi@example.com',
                UserColumns::PROVINSI       => 'Yogyakarta',
                UserColumns::KABUPATEN      => 'Sleman',
                UserColumns::KECAMATAN      => 'Depok',
                UserColumns::JALAN          => 'Jl. Kaliurang Km 7',
                UserColumns::KODE_POS       => '55281',
                UserColumns::TANGGAL_LAHIR  => 15,
                UserColumns::BULAN_LAHIR    => 8,
                UserColumns::TAHUN_LAHIR    => 1990,
                UserColumns::USIA           => 35,
                'password'                  => Hash::make('password123'),
                'created_at'                => now(),
                'updated_at'                => now(),
            ],
        ]);
    }
}
