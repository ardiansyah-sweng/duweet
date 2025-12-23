<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'           => 'Test User',
                'first_name'     => 'Test',
                'last_name'      => 'User',
                'provinsi'       => 'DKI Jakarta',
                'kabupaten'      => 'Jakarta Selatan',
                'kecamatan'      => 'Kebayoran Baru',
                'jalan'          => 'Jl. Senopati No. 1',
                'kode_pos'       => '12190',
                'tanggal_lahir'  => 15,
                'bulan_lahir'    => 8,
                'tahun_lahir'    => 2002,
                'usia'           => 21,
            ]
        );

        $this->call([
            FinancialAccountSeeder::class,
            UserSeeder::class,
            UserAccountSeeder::class,
            AccountSeeder::class,
            FinancialAccountSeeder::class,
            // AccountSeeder::class,
            UserTelephoneSeeder::class,
            UserFinancialAccountSeeder::class,
            TransactionSeeder::class,
            DemoDataSeeder::class,
            //AccountSeeder::class,
        ]);
    }
}