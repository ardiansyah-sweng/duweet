<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Constants\UserFinancialAccountColumns;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = Carbon::now();

            // ==========================================
            // 1️⃣ USERS
            // ==========================================
            User::create([
                'name'           => 'Rafi Aulia',
                'first_name'     => 'Rafi',
                'last_name'      => 'Aulia',
                'email'          => 'rafi@example.com',
                'provinsi'       => 'Jawa Barat',
                'kabupaten'      => 'Purwakarta',
                'kecamatan'      => 'Wanayasa',
                'jalan'          => 'Kp. Krajan No. 35',
                'kode_pos'       => '41174',
                'usia'           => 23,
                'bulan_lahir'    => 8,
                'tanggal_lahir'  => 15,
                'tahun_lahir'    => 2002,
            ]);

            User::create([
                'name'           => 'Siti Nurhaliza',
                'first_name'     => 'Siti',
                'last_name'      => 'Nurhaliza',
                'email'          => 'siti@example.com',
                'provinsi'       => 'DKI Jakarta',
                'kabupaten'      => 'Jakarta Pusat',
                'kecamatan'      => 'Menteng',
                'jalan'          => 'Jl. Thamrin No. 1',
                'kode_pos'       => '10310',
                'usia'           => 23,
                'bulan_lahir'    => 3,
                'tanggal_lahir'  => 20,
                'tahun_lahir'    => 2000,
            ]);

            // ==========================================
            // 2️⃣ FINANCIAL ACCOUNTS
            // ==========================================
            $accIdKas = DB::table('financial_accounts')->insertGetId([
                'name'            => 'Kas Utama',
                'type'            => 'AS',
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
                'type'            => 'EX',
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
                'type'            => 'IN',
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

            // ==========================================
            // 3️⃣ USER-FINANCIAL ACCOUNTS
            // ==========================================
            DB::table(config('db_tables.user_financial_account'))->insert([
                [
                    UserFinancialAccountColumns::USER_ID              => 1,
                    UserFinancialAccountColumns::FINANCIAL_ACCOUNT_ID => $accIdKas,
                    UserFinancialAccountColumns::INITIAL_BALANCE      => 1000000,
                    UserFinancialAccountColumns::BALANCE              => 1000000,
                    UserFinancialAccountColumns::IS_ACTIVE            => true,
                    'created_at'                                      => $now,
                    'updated_at'                                      => $now,
                ],
                [
                    UserFinancialAccountColumns::USER_ID              => 2,
                    UserFinancialAccountColumns::FINANCIAL_ACCOUNT_ID => $accIdBiaya,
                    UserFinancialAccountColumns::INITIAL_BALANCE      => 500000,
                    UserFinancialAccountColumns::BALANCE              => 500000,
                    UserFinancialAccountColumns::IS_ACTIVE            => true,
                    'created_at'                                      => $now,
                    'updated_at'                                      => $now,
                ],
            ]);

            // ==========================================
            // 4️⃣ TRANSACTIONS
            // ==========================================
            DB::table('transactions')->insert([
                [
                    'transaction_group_id' => (string) Str::uuid(),
                    'user_id'              => 1,
                    'financial_account_id' => $accIdBiaya,
                    'entry_type'           => 'debit',
                    'amount'               => 150000,
                    'balance_effect'       => 'decrease',
                    'description'          => 'Pembelian alat tulis kantor',
                    'is_balance'           => false,
                    'created_at'           => '2025-11-01 08:30:00',
                    'updated_at'           => '2025-11-01 08:30:00',
                ],
                [
                    'transaction_group_id' => (string) Str::uuid(),
                    'user_id'              => 1,
                    'financial_account_id' => $accIdBiaya,
                    'entry_type'           => 'debit',
                    'amount'               => 200000,
                    'balance_effect'       => 'decrease',
                    'description'          => 'Pembelian bahan bakar',
                    'is_balance'           => false,
                    'created_at'           => '2025-11-05 10:00:00',
                    'updated_at'           => '2025-11-05 10:00:00',
                ],
                [
                    'transaction_group_id' => (string) Str::uuid(),
                    'user_id'              => 2,
                    'financial_account_id' => $accIdBiaya,
                    'entry_type'           => 'debit',
                    'amount'               => 50000,
                    'balance_effect'       => 'decrease',
                    'description'          => 'Biaya transportasi',
                    'is_balance'           => false,
                    'created_at'           => '2025-11-02 09:00:00',
                    'updated_at'           => '2025-11-02 09:00:00',
                ],
                [
                    'transaction_group_id' => (string) Str::uuid(),
                    'user_id'              => 2,
                    'financial_account_id' => $accIdBiaya,
                    'entry_type'           => 'debit',
                    'amount'               => 100000,
                    'balance_effect'       => 'decrease',
                    'description'          => 'Biaya konsumsi rapat',
                    'is_balance'           => false,
                    'created_at'           => '2025-11-08 15:00:00',
                    'updated_at'           => '2025-11-08 15:00:00',
                ],
            ]);
        });

        $this->command->info('✅ DemoDataSeeder selesai.');
    }
}