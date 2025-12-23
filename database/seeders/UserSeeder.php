<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Constants\UserColumns;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user manual dengan nama spesifik
        User::create([
            'name' => 'Dimas Pratama Anugraha',
            'first_name' => 'Dimas',
            'middle_name' => 'Pratama',
            'last_name' => 'Anugraha',
            'email' => 'dims@example.com',
            'provinsi' => 'DKI Jakarta',
            'kabupaten' => 'Jakarta Selatan',
            'kecamatan' => 'Kebayoran Baru',
            'jalan' => 'Jl. Sudirman No. 123',
            'kode_pos' => '12190',
            'tanggal_lahir' => 15,
            'bulan_lahir' => 8,
            'tahun_lahir' => 1995,
            'usia' => 30,
        ]);

        User::create([
            'name' => 'Muhammad Abdullah azzam',
            'first_name' => 'Muhammad',
            'middle_name' => 'Abdullah',
            'last_name' => 'azzam',
            'email' => 'azzam@example.com',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Bandung',
            'kecamatan' => 'Coblong',
            'jalan' => 'Jl. Dago No. 45',
            'kode_pos' => '40135',
            'tanggal_lahir' => 22,
            'bulan_lahir' => 3,
            'tahun_lahir' => 1998,
            'usia' => 27,
        ]);

        User::create([
            'name' => 'Bagas Ratmanta',
            'first_name' => 'Bagas',
            'middle_name' => null,
            'last_name' => 'Ratmanta',
            'email' => 'bagas@example.com',
            'provinsi' => 'Jawa Tengah',
            'kabupaten' => 'Semarang',
            'kecamatan' => 'Semarang Tengah',
            'jalan' => 'Jl. Pandanaran No. 78',
            'kode_pos' => '50134',
            'tanggal_lahir' => 10,
            'bulan_lahir' => 12,
            'tahun_lahir' => 1992,
            'usia' => 33,
        ]);

        // Tetap gunakan factory untuk data tambahan
        User::factory(7)->create();
    }
}
