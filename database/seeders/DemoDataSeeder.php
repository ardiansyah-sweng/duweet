<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

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
                'name'          => 'Rafi Satya',
                'email'         => 'rafi@example.com',
                'password'      => Hash::make('password'),
                'usia'          => 21,
                'bulan_lahir'   => 8,      // month
                'tanggal_lahir' => 15,     // day-of-month (1–31)
                'tahun_lahir'   => 2002,   // year (smallint)
                'email_verified_at' => now(),
                'remember_token'    => Str::random(10),
            ]);

            User::create([
                'name'          => 'Andi Nugraha',
                'email'         => 'andi@example.com',
                'password'      => Hash::make('password'),
                'usia'          => 22,
                'bulan_lahir'   => 11,     // month
                'tanggal_lahir' => 20,     // day-of-month (1–31)
                'tahun_lahir'   => 2001,   // year (smallint)
                'email_verified_at' => now(),
                'remember_token'    => Str::random(10),
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
            DB::table('user_financial_accounts')->insert([
                [
                    'user_id'              => 1,
                    'financial_account_id' => $accIdKas,
                    'initial_balance'      => 1000000,
                    'balance'              => 1000000,
                    'is_active'            => true,
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ],
                [
                    'user_id'              => 2,
                    'financial_account_id' => $accIdBiaya,
                    'initial_balance'      => 500000,
                    'balance'              => 500000,
                    'is_active'            => true,
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ],
            ]);

            // ==========================================
            // 4️⃣ TRANSACTIONS (biar bisa diuji by period)
            // ==========================================
            DB::table('transactions')->insert([
                // === Rafi: beberapa transaksi dengan tanggal berbeda ===
                [
                    'transaction_group_id' => (string) Str::uuid(),
                    'user_id'              => 1,
                    'financial_account_id'  => $accIdBiaya,
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
                    'financial_account_id'           => $accIdBiaya,
                    'entry_type'           => 'debit',
                    'amount'               => 200000,
                    'balance_effect'       => 'decrease',
                    'description'          => 'Pembelian bahan bakar',
                    'is_balance'           => false,
                    'created_at'           => '2025-11-05 10:00:00',
                    'updated_at'           => '2025-11-05 10:00:00',
                ],

                // === Andi: transaksi dalam periode berbeda ===
                [
                    'transaction_group_id' => (string) Str::uuid(),
                    'user_id'              => 2,
                    'financial_account_id'           => $accIdBiaya,
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
                    'financial_account_id'  => $accIdBiaya,
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

        $this->command->info('✅ DemoDataSeeder selesai dan siap untuk query by period test.');
    }
}