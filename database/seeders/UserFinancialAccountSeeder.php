<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class UserFinancialAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have a few users (including test@example.com) so the seed produces more visible rows.
        $targetUsers = 5;
        $currentUsers = User::count();
        if ($currentUsers < $targetUsers) {
            User::factory($targetUsers - $currentUsers)->create();
        }

        $faTable = config('db_tables.financial_account');
        $uaTable = config('db_tables.user_account', 'user_accounts');

        // Ensure user_accounts exist for every user (idempotent)
        foreach (User::all() as $user) {
            $exists = DB::table($uaTable)->where('id_user', $user->id)->first();
            if (! $exists) {
                $row = [
                    'id_user' => $user->id,
                    'username' => 'user' . $user->id,
                    'email' => $user->email,
                    'password' => bcrypt('password'),
                    'verified_at' => now(),
                    'is_active' => true,
                ];

                // Add timestamps only if columns exist
                if (Schema::hasColumn($uaTable, 'created_at')) {
                    $row['created_at'] = now();
                }
                if (Schema::hasColumn($uaTable, 'updated_at')) {
                    $row['updated_at'] = now();
                }

                DB::table($uaTable)->insert($row);
            }
        }

        // Load available financial accounts
        $accounts = DB::table($faTable)->select('id', 'name', 'initial_balance', 'type')->orderBy('id')->get();
        if ($accounts->isEmpty()) {
            return; // nothing to attach
        }

        // Define a fixed list of 10 account names that we will attach to each user
        $desiredAccountNames = [
            'Dompet', 'Kas Kecil', 'BCA Tabungan', 'Mandiri Tabungan', 'BNI Tabungan',
            'BRI Simpedes', 'GoPay', 'OVO', 'Dana', 'ShopeePay'
        ];

        // Map available accounts by name for quick lookup
        $accountsByName = $accounts->keyBy('name');

        // Define explicit, deterministic balances per account (same values for each user)
        $fixedBalances = [
            'Dompet' => 50000,
            'Kas Kecil' => 100000,
            'BCA Tabungan' => 5000000,
            'Mandiri Tabungan' => 3000000,
            'BNI Tabungan' => 2000000,
            'BRI Simpedes' => 1500000,
            'GoPay' => 200000,
            'OVO' => 150000,
            'Dana' => 100000,
            'ShopeePay' => 75000,
        ];

        foreach (User::all() as $user) {
            foreach ($desiredAccountNames as $name) {
                if (! isset($accountsByName[$name])) {
                    // skip missing account names
                    continue;
                }
                $acc = $accountsByName[$name];

                // Use fixed, declared balances instead of random values. If account has an initial_balance set
                // in the financial_accounts table, prefer that; otherwise use our fixed mapping.
                if ($acc->initial_balance !== null) {
                    $bal = $acc->initial_balance;
                } elseif (isset($fixedBalances[$name])) {
                    $bal = $fixedBalances[$name];
                } else {
                    $bal = 50000; // fallback
                }

                DB::table('user_financial_accounts')->updateOrInsert(
                    ['user_id' => $user->id, 'financial_account_id' => $acc->id],
                    [
                        'balance' => $bal,
                        'initial_balance' => $bal,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
