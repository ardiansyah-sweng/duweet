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
                'name'          => 'Rafi Satya',
                'first_name'    => 'Rafi',
                'middle_name'   => null,
                'last_name'     => 'Satya',
                'email'         => 'rafi@example.com',
                'provinsi'      => 'Jawa Barat',
                'kabupaten'     => 'Bandung',
                'kecamatan'     => 'Coblong',
                'jalan'         => 'Jl. Dago No. 123',
                'kode_pos'      => '40135',
                'usia'          => 21,
                'tanggal_lahir' => 15,
                'bulan_lahir'   => 8,
                'tahun_lahir'   => 2002,
            ]);

            $userIdAndi = DB::table('users')->insertGetId([
                'name'          => 'Andi Nugraha',
                'first_name'    => 'Andi',
                'middle_name'   => null,
                'last_name'     => 'Nugraha',
                'email'         => 'andi@example.com',
                'provinsi'      => 'Jawa Barat',
                'kabupaten'     => 'Sumedang',
                'kecamatan'     => 'Jatinangor',
                'jalan'         => 'Jl. Raya No. 45',
                'kode_pos'      => '45363',
                'usia'          => 22,
                'tanggal_lahir' => 20,
                'bulan_lahir'   => 11,
                'tahun_lahir'   => 2001,
            ]);

            // Asset (AS)
            $accIdKas = DB::table('financial_accounts')->insertGetId([
                'name'            => 'Kas Utama',
                'type'            => 'AS',        // Asset
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
                'type'            => 'LI',     
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

            // Income (IN)
            $accIdPendapatan = DB::table('financial_accounts')->insertGetId([
                'name'            => 'Pendapatan Penjualan',
                'type'            => 'AS',    
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
                    'financial_account_id' => $accIdPendapatan,
                    'initial_balance'      => 200000,
                    'balance'              => 200000,
                    'is_active'            => true,
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ],
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
