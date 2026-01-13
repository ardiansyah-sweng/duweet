<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Sesuaikan dengan skema users saat ini (tidak ada password / timestamps / email_verified_at)
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
            'tanggal_lahir' => 15,
            'bulan_lahir'   => 8,
            'tahun_lahir'   => 2002,
            'usia'          => 21,
        ];

        if (Schema::hasColumn('users', 'password')) {
            $attrs['password'] = bcrypt('password');
        }
        if (Schema::hasColumn('users', 'remember_token')) {
            $attrs['remember_token'] = str()->random(10);
        }
        if (Schema::hasColumn('users', 'email_verified_at')) {
            $attrs['email_verified_at'] = now();
        }

        User::updateOrCreate(['email' => $attrs['email']], $attrs);

        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'email' => 'test@example.com',
                'name' => 'Test User',
            ]);
        }

        // Run seeders in a sensible order and avoid duplicates
        $this->call([
            FinancialAccountSeeder::class,
            UserSeeder::class,
            UserAccountSeeder::class,
            AccountSeeder::class,
            UserTelephoneSeeder::class,
            UserFinancialAccountSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
