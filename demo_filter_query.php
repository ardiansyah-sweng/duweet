#!/usr/bin/env php
<?php

/**
 * Demo Script - Filter Account Berdasarkan Type
 * Mendemonstrasikan berbagai cara filtering account
 */

echo "\n";
echo str_repeat("=", 70) . "\n";
echo "DEMO - FILTER ACCOUNT BERDASARKAN ACCOUNT TYPE\n";
echo str_repeat("=", 70) . "\n\n";

// Sample data
$accounts = [
    (object)[
        'id' => 1,
        'name' => 'Bank Utama',
        'type' => 'AS',
        'balance' => 1000000,
        'is_active' => true,
        'is_group' => false,
    ],
    (object)[
        'id' => 2,
        'name' => 'Properti',
        'type' => 'AS',
        'balance' => 5000000,
        'is_active' => true,
        'is_group' => false,
    ],
    (object)[
        'id' => 3,
        'name' => 'Gaji',
        'type' => 'IN',
        'balance' => 0,
        'is_active' => true,
        'is_group' => true,
    ],
    (object)[
        'id' => 4,
        'name' => 'Bonus',
        'type' => 'IN',
        'balance' => 500000,
        'is_active' => true,
        'is_group' => false,
    ],
    (object)[
        'id' => 5,
        'name' => 'Makan',
        'type' => 'EX',
        'balance' => 500000,
        'is_active' => true,
        'is_group' => false,
    ],
    (object)[
        'id' => 6,
        'name' => 'Utang Kartu Kredit',
        'type' => 'LI',
        'balance' => 2000000,
        'is_active' => true,
        'is_group' => false,
    ],
    (object)[
        'id' => 7,
        'name' => 'Belanja',
        'type' => 'SP',
        'balance' => 1000000,
        'is_active' => true,
        'is_group' => false,
    ],
    (object)[
        'id' => 8,
        'name' => 'Akun Tidak Aktif',
        'type' => 'AS',
        'balance' => 0,
        'is_active' => false,
        'is_group' => false,
    ],
];

// Helper functions
function filterByType($accounts, $type) {
    return array_filter($accounts, fn($acc) => $acc->type === $type);
}

function filterByTypes($accounts, $types) {
    $types = is_array($types) ? $types : explode(',', $types);
    $types = array_map('trim', $types);
    return array_filter($accounts, fn($acc) => in_array($acc->type, $types));
}

function filterActive($accounts) {
    return array_filter($accounts, fn($acc) => $acc->is_active === true);
}

function filterGroups($accounts) {
    return array_filter($accounts, fn($acc) => $acc->is_group === true);
}

function printAccounts($accounts, $title = '') {
    if ($title) {
        echo "   $title\n";
    }
    if (empty($accounts)) {
        echo "   (Tidak ada data)\n";
        return;
    }
    foreach ($accounts as $acc) {
        echo "   - ID: {$acc->id} | {$acc->name} | Type: {$acc->type} | Balance: " . number_format($acc->balance) . "\n";
    }
}

function summarizeByType($accounts) {
    $summary = [];
    foreach ($accounts as $acc) {
        if (!isset($summary[$acc->type])) {
            $summary[$acc->type] = ['count' => 0, 'total_balance' => 0];
        }
        $summary[$acc->type]['count']++;
        $summary[$acc->type]['total_balance'] += $acc->balance;
    }
    return $summary;
}

// Test 1
echo "TEST 1 - Filter berdasarkan tipe ASET (AS)\n";
echo str_repeat("-", 70) . "\n";
$assets = filterByType($accounts, 'AS');
echo "Total: " . count($assets) . " akun\n";
printAccounts($assets);
echo "\n";

// Test 2
echo "TEST 2 - Filter berdasarkan tipe INCOME dan EXPENSES\n";
echo str_repeat("-", 70) . "\n";
$incomeExpense = filterByTypes($accounts, ['IN', 'EX']);
echo "Total: " . count($incomeExpense) . " akun\n";
printAccounts($incomeExpense);
echo "\n";

// Test 3
echo "TEST 3 - Filter dengan string terpisah koma (SP,LI)\n";
echo str_repeat("-", 70) . "\n";
$spendingLiability = filterByTypes($accounts, 'SP,LI');
echo "Total: " . count($spendingLiability) . " akun\n";
printAccounts($spendingLiability);
echo "\n";

// Test 4
echo "TEST 4 - Filter akun yang AKTIF\n";
echo str_repeat("-", 70) . "\n";
$activeAccounts = filterActive($accounts);
echo "Total: " . count($activeAccounts) . " akun\n";
printAccounts($activeAccounts);
echo "\n";

// Test 5
echo "TEST 5 - Filter akun ASET yang AKTIF\n";
echo str_repeat("-", 70) . "\n";
$activeAssets = filterActive(filterByType($accounts, 'AS'));
echo "Total: " . count($activeAssets) . " akun\n";
printAccounts($activeAssets);
echo "\n";

// Test 6
echo "TEST 6 - Filter hanya GRUP akun\n";
echo str_repeat("-", 70) . "\n";
$groups = filterGroups($accounts);
echo "Total: " . count($groups) . " akun\n";
printAccounts($groups);
echo "\n";

// Test 7
echo "TEST 7 - Summary akun per tipe\n";
echo str_repeat("-", 70) . "\n";
$summary = summarizeByType($accounts);
echo "Total tipe: " . count($summary) . "\n";
foreach ($summary as $type => $data) {
    echo "   - Tipe: $type | Count: {$data['count']} | Total Balance: " . number_format($data['total_balance']) . "\n";
}
echo "\n";

// Test 8
echo "TEST 8 - Summary akun ASET per tipe\n";
echo str_repeat("-", 70) . "\n";
$activeSummary = summarizeByType(filterActive($accounts));
echo "Total tipe aktif: " . count($activeSummary) . "\n";
foreach ($activeSummary as $type => $data) {
    echo "   - Tipe: $type | Count: {$data['count']} | Total Balance: " . number_format($data['total_balance']) . "\n";
}
echo "\n";

// Test 9
echo "TEST 9 - Semua akun\n";
echo str_repeat("-", 70) . "\n";
echo "Total: " . count($accounts) . " akun\n";
printAccounts($accounts);
echo "\n";

// Summary table
echo "TEST 10 - Tabel Referensi Tipe Akun\n";
echo str_repeat("-", 70) . "\n";
$types = [
    'AS' => 'Aset',
    'IN' => 'Pendapatan',
    'EX' => 'Beban',
    'SP' => 'Pengeluaran',
    'LI' => 'Kewajiban',
];
echo "   " . str_pad('Kode', 8) . " | " . str_pad('Label', 15) . " | " . str_pad('Jumlah', 8) . "\n";
echo "   " . str_repeat("-", 40) . "\n";
foreach ($types as $code => $label) {
    $typeAccounts = filterByType($accounts, $code);
    echo "   " . str_pad($code, 8) . " | " . str_pad($label, 15) . " | " . str_pad(count($typeAccounts), 8) . "\n";
}
echo "\n";

echo str_repeat("=", 70) . "\n";
echo "✓ DEMO SELESAI\n";
echo str_repeat("=", 70) . "\n\n";

echo "QUERY ELOQUENT YANG DIGUNAKAN DI LARAVEL:\n";
echo str_repeat("-", 70) . "\n";
echo "1. FinancialAccount::ofType('AS')->get();\n";
echo "2. FinancialAccount::ofType(['IN', 'EX'])->get();\n";
echo "3. FinancialAccount::ofType('SP,LI')->get();\n";
echo "4. FinancialAccount::active()->get();\n";
echo "5. FinancialAccount::ofType('AS')->active()->get();\n";
echo "6. FinancialAccount::groups()->get();\n";
echo "7. FinancialAccount::summaryByType();\n";
echo "8. FinancialAccount::groupedByType();\n";
echo "9. \$user->getAccountsByType('AS');\n";
echo "10. \$user->getActiveAccounts();\n";
echo str_repeat("-", 70) . "\n\n";

?>
