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
        // Pastikan kolom alamat yang wajib diisi karena tidak nullable di migration
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
            // Birth data (integer fields per current migration)
            'tanggal_lahir' => 15,
            'bulan_lahir'   => 8,
            'tahun_lahir'   => 2002,
            'usia'          => 21,
        ];

        // Kolom opsional yang mungkin ada pada skema lama: password / email_verified_at / remember_token
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

        // Create a test user only if not already exists
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'email' => 'test@example.com',
                'name' => 'Test User',
            ]);
        }

        // Run seeders in order: users -> user_accounts -> financial_accounts -> transactions
        $this->call([
            UserSeeder::class,
            UserAccountSeeder::class,
            AccountSeeder::class,
            FinancialAccountSeeder::class,
            UserFinancialAccountSeeder::class,
            TransactionSeeder::class,
            //AccountSeeder::class,
        ]);
    }
}