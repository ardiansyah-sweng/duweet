<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;


class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = Carbon::now();

            $userIdRafi = DB::table('users')->insertGetId([
                'name'              => 'Rafi Satya',
                'email'             => 'rafi@example.com',
                'password'          => Hash::make('rahasia123'),
                'usia'              => 21,
                'bulan_lahir'       => 8,
                'tanggal_lahir'     => '2002-08-15',
                'email_verified_at' => $now,
                'remember_token'    => Str::random(10),
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            $userIdAndi = DB::table('users')->insertGetId([
                'name'              => 'Andi Nugraha',
                'email'             => 'andi@example.com',
                'password'          => Hash::make('rahasia123'),
                'usia'              => 22,
                'bulan_lahir'       => 11,
                'tanggal_lahir'     => '2001-11-20',
                'email_verified_at' => $now,
                'remember_token'    => Str::random(10),
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            $accIdKas = DB::table('financial_accounts')->insertGetId([
                'name'            => 'Kas Utama',
                'type'            => 'LI',        // Asset
                'balance'         => 1000000,
                'initial_balance' => 1000000,
                'is_group'        => false,
                'description'     => 'Saldo utama perusahaan',
                'is_active'       => true,
                'sort_order'      => 1,
                'level'           => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);

            $accIdBiaya = DB::table('financial_accounts')->insertGetId([
                'name'            => 'Biaya Operasional',
                'type'            => 'AS',        // Expense
                'balance'         => 500000,
                'initial_balance' => 500000,
                'is_group'        => false,
                'description'     => 'Pengeluaran bulanan kantor',
                'is_active'       => true,
                'sort_order'      => 2,
                'level'           => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);

            $accIdPendapatan = DB::table('financial_accounts')->insertGetId([
                'name'            => 'Pendapatan Penjualan',
                'type'            => 'IN',        // Income
                'balance'         => 200000,
                'initial_balance' => 200000,
                'is_group'        => false,
                'description'     => 'Pemasukan hasil penjualan produk',
                'is_active'       => true,
                'sort_order'      => 3,
                'level'           => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);

            DB::table('user_financial_accounts')->insert([
                [
                    'user_id'              => $userIdRafi,
                    'financial_account_id' => $accIdKas,
                    'initial_balance'      => 1000000,
                    'balance'              => 1000000,
                    'is_active'            => true,
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ],
                [
                    'user_id'              => $userIdAndi,
                    'financial_account_id' => $accIdBiaya,
                    'initial_balance'      => 500000,
                    'balance'              => 500000,
                    'is_active'            => true,
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ],
            ]);
        });

        $this->command->info('✅ DemoDataSeeder selesai tanpa FK error.');

    }
}
