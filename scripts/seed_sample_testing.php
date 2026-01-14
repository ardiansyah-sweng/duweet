<?php
// Seed minimal sample data into the testing database and print summary
$_ENV['APP_ENV'] = 'testing';
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// helper to get table name from config or fallback
function t($key, $fallback) {
    $cfg = config('db_tables');
    return $cfg[$key] ?? $fallback;
}

$usersTable = t('user', 'users');
$userAccountsTable = t('user_account', 'user_accounts');
$financialAccountsTable = t('financial_account', 'financial_accounts');
$userFinancialTable = t('user_financial_account', 'user_financial_accounts');

// Clear existing data in testing
DB::table($userFinancialTable)->delete();
DB::table($userAccountsTable)->delete();
DB::table($usersTable)->delete();
DB::table($financialAccountsTable)->delete();

$now = now();

// Insert users
$u1 = DB::table($usersTable)->insertGetId([
    'name' => 'Sample One',
    'email' => 'sample1@example.com',
    'provinsi' => 'P', 'kabupaten' => 'K', 'kecamatan' => 'C', 'jalan' => 'Jalan', 'kode_pos' => '00000',
    'tanggal_lahir' => 1, 'bulan_lahir' => 1, 'tahun_lahir' => 1990, 'usia' => 36,
    'created_at' => $now, 'updated_at' => $now,
]);

$u2 = DB::table($usersTable)->insertGetId([
    'name' => 'Sample Two',
    'email' => 'sample2@example.com',
    'provinsi' => 'P', 'kabupaten' => 'K', 'kecamatan' => 'C', 'jalan' => 'Jalan', 'kode_pos' => '00000',
    'tanggal_lahir' => 1, 'bulan_lahir' => 1, 'tahun_lahir' => 1995, 'usia' => 31,
    'created_at' => $now, 'updated_at' => $now,
]);

// Insert user_accounts
$ua1 = DB::table($userAccountsTable)->insertGetId([
    'id_user' => $u1, 'username' => 'sample1', 'email' => 'sample1@example.com', 'password' => 'x', 'verified_at' => null, 'is_active' => 1
]);

$ua2 = DB::table($userAccountsTable)->insertGetId([
    'id_user' => $u2, 'username' => 'sample2', 'email' => 'sample2@example.com', 'password' => 'x', 'verified_at' => null, 'is_active' => 1
]);

// Insert financial accounts
$fa1 = DB::table($financialAccountsTable)->insertGetId([
    'name' => 'Cash', 'type' => 'AS', 'balance' => 0, 'initial_balance' => 0, 'is_group' => 0, 'description' => null, 'is_active' => 1, 'is_liquid' => 1, 'sort_order' => 0, 'level' => 0, 'created_at' => $now, 'updated_at' => $now,
]);

$fa2 = DB::table($financialAccountsTable)->insertGetId([
    'name' => 'Bank', 'type' => 'AS', 'balance' => 0, 'initial_balance' => 0, 'is_group' => 0, 'description' => null, 'is_active' => 1, 'is_liquid' => 1, 'sort_order' => 0, 'level' => 0, 'created_at' => $now, 'updated_at' => $now,
]);

$fa3 = DB::table($financialAccountsTable)->insertGetId([
    'name' => 'Credit', 'type' => 'LI', 'balance' => 0, 'initial_balance' => 0, 'is_group' => 0, 'description' => null, 'is_active' => 1, 'is_liquid' => 0, 'sort_order' => 0, 'level' => 0, 'created_at' => $now, 'updated_at' => $now,
]);

// Map user_financial_accounts: ua1 has 2 accounts, ua2 has 1
DB::table($userFinancialTable)->insert([
    ['user_account_id' => $ua1, 'financial_account_id' => $fa1, 'initial_balance' => 0, 'balance' => 1000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
    ['user_account_id' => $ua1, 'financial_account_id' => $fa2, 'initial_balance' => 0, 'balance' => 2000, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
    ['user_account_id' => $ua2, 'financial_account_id' => $fa3, 'initial_balance' => 0, 'balance' => -500, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
]);

echo "Inserted sample data:\n";
echo "users: {$u1}, {$u2}\n";
echo "user_accounts: {$ua1}, {$ua2}\n";
echo "financial_accounts: {$fa1}, {$fa2}, {$fa3}\n";

return 0;
