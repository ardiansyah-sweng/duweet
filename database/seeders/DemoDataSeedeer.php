<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoDataSeedeer extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = Carbon::now();

            /** -----------------------------------------------------
             * 1️⃣ USERS (profil dasar)
             * ---------------------------------------------------- */
            $userRafi = DB::table('users')->insertGetId([
                'name'              => 'Dimas yayayya',
                'email'             => 'DImasJomok@example.com',
                'usia'              => 21,
                'bulan_lahir'       => 8,
                'tanggal_lahir'     => '2002-08-15',
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            $userAndi = DB::table('users')->insertGetId([
                'name'              => 'Azzamer Nugraha',
                'email'             => 'pdf.profile@example.com',
                'usia'              => 22,
                'bulan_lahir'       => 11,
                'tanggal_lahir'     => '2001-11-20',
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            $userBudi = DB::table('users')->insertGetId([
                'name'              => 'ekoeger',
                'email'             => 'eko.profile@example.com',
                'usia'              => 23,
                'bulan_lahir'       => 2,
                'tanggal_lahir'     => '2000-02-10',
                'email_verified_at' => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            /** -----------------------------------------------------
             * 2️⃣ USER ACCOUNTS (login credentials)
             * ---------------------------------------------------- */
            $uaRafi = DB::table('user_accounts')->insertGetId([
                'id_user'           => $userRafi,
                'username'          => 'Dimas',
                'email'             => 'dimas@example.com',
                'password'          => Hash::make('rahasia123'),
                'verified_at' => $now,
                'is_active'         => true,
                // 'created_at'        => $now,
                // 'updated_at'        => $now,
            ]);

            $uaAndi = DB::table('user_accounts')->insertGetId([
                'id_user'           => $userAndi,
                'username'          => 'Sarah',
                'email'             => 'sarah@example.com',
                'password'          => Hash::make('rahasia123'),
                'verified_at' => $now,
                'is_active'         => false, // Nonaktif untuk uji query
                // 'created_at'        => $now,
                // 'updated_at'        => $now,
            ]);

            // Budi tidak punya akun login — untuk menguji “tanpa user_account”

            /** -----------------------------------------------------
             * 3️⃣ FINANCIAL ACCOUNTS (tipe akun keuangan)
             * ---------------------------------------------------- */
            $accKas = DB::table('financial_accounts')->insertGetId([
                'name'            => 'Kas Utama',
                'type'            => 'AS',
                'balance'         => 1500000,
                'initial_balance' => 1500000,
                'is_group'        => false,
                'description'     => 'Saldo kas utama perusahaan',
                'is_active'       => true,
                'sort_order'      => 1,
                'level'           => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);

            $accOperasional = DB::table('financial_accounts')->insertGetId([
                'name'            => 'Biaya Operasional',
                'type'            => 'EX',
                'balance'         => 500000,
                'initial_balance' => 500000,
                'is_group'        => false,
                'description'     => 'Biaya bulanan operasional',
                'is_active'       => true,
                'sort_order'      => 2,
                'level'           => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        });

        $this->command->info('✅ DemoDataSeeder selesai. Data siap untuk uji Query Pencarian User.');
    }
}