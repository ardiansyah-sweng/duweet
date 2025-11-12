<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\FinancialAccount;
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

            // Create Financial Accounts using Model method (not raw query)
            // Asset (AS) - Kas Utama
            $accountKas = FinancialAccount::createForUser([
                'user_id'         => $userIdRafi,
                'name'            => 'Kas Utama',
                'type'            => 'AS',
                'initial_balance' => 1000000,
                'description'     => 'Saldo utama perusahaan',
                'is_group'        => false,
            ]);

            // Liability (LI) - Hutang Operasional  
            $accountHutang = FinancialAccount::createForUser([
                'user_id'         => $userIdAndi,
                'name'            => 'Hutang Operasional',
                'type'            => 'LI',
                'initial_balance' => 500000,
                'description'     => 'Hutang operasional perusahaan',
                'is_group'        => false,
            ]);

            // Asset (AS) - Pendapatan Penjualan (Asset karena mencatat kas masuk)
            $accountPendapatan = FinancialAccount::createForUser([
                'user_id'         => $userIdRafi,
                'name'            => 'Kas dari Penjualan',
                'type'            => 'AS',
                'initial_balance' => 200000,
                'description'     => 'Kas hasil penjualan produk',
                'is_group'        => false,
            ]);
        });

        $this->command->info('✅ DemoDataSeeder selesai tanpa FK error.');

    }
}
