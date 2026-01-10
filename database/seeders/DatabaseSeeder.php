<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Data user default
        $attrs = [
            'name'          => 'Test User',
            'first_name'    => 'Test',
            'middle_name'   => null,
            'last_name'     => 'User',
            'email'         => 'test@example.com',
            'provinsi'      => 'Jawa Barat',
            'kabupaten'     => 'Bandung',
            'kecamatan'     => 'Coblong',
            'jalan'         => 'Jl. Dago No. 123',
            'kode_pos'      => '40135',
            // Birth data
            'tanggal_lahir' => 15,
            'bulan_lahir'   => 8,
            'tahun_lahir'   => 2002,
            'usia'          => 21,
        ];

        // Kolom opsional
        if (Schema::hasColumn('users', 'password')) {
            $attrs['password'] = bcrypt('password');
        }
        if (Schema::hasColumn('users', 'remember_token')) {
            $attrs['remember_token'] = str()->random(10);
        }
        if (Schema::hasColumn('users', 'email_verified_at')) {
            $attrs['email_verified_at'] = now();
        }

        // Update/create test user
        User::updateOrCreate(['email' => $attrs['email']], $attrs);

        // Buat test user jika belum ada
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'email' => 'test@example.com',
                'name'  => 'Test User',
            ]);
        }

        // Pemanggilan semua seeder dari kedua kode (digabung)
        $this->call([
            // Dari kode pertama
            FinancialAccountSeeder::class,
            UserSeeder::class,
            UserAccountSeeder::class,
            AccountSeeder::class,
            UserTelephoneSeeder::class,
            UserFinancialAccountSeeder::class,
            TransactionSeeder::class,

            // Dari kode kedua
            CashoutSeeder::class,
        ]);
    }
}
