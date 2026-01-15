<?php
/**
 * Demo Query - Financial Account Filter
 * Menampilkan contoh query dan hasil yang diharapkan
 */

echo "\n";
echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║     DEMO: Financial Account Filter by User & Type            ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Mock data untuk demonstrasi
$mockUsers = [
    ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
    ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
];

$mockAccounts = [
    ['id' => 1, 'name' => 'Kas', 'type' => 'AS', 'is_active' => 1],
    ['id' => 2, 'name' => 'Bank BCA', 'type' => 'AS', 'is_active' => 1],
    ['id' => 3, 'name' => 'Gaji', 'type' => 'IN', 'is_active' => 1],
    ['id' => 4, 'name' => 'Bonus', 'type' => 'IN', 'is_active' => 1],
    ['id' => 5, 'name' => 'Belanja', 'type' => 'EX', 'is_active' => 1],
    ['id' => 6, 'name' => 'Transportasi', 'type' => 'SP', 'is_active' => 1],
    ['id' => 7, 'name' => 'Hutang KPR', 'type' => 'LI', 'is_active' => 1],
];

$mockUserAccounts = [
    ['user_id' => 1, 'account_id' => 1, 'account_name' => 'Kas', 'type' => 'AS', 'balance' => 5000000],
    ['user_id' => 1, 'account_id' => 2, 'account_name' => 'Bank BCA', 'type' => 'AS', 'balance' => 15000000],
    ['user_id' => 1, 'account_id' => 3, 'account_name' => 'Gaji', 'type' => 'IN', 'balance' => 0],
    ['user_id' => 1, 'account_id' => 4, 'account_name' => 'Bonus', 'type' => 'IN', 'balance' => 0],
    ['user_id' => 1, 'account_id' => 5, 'account_name' => 'Belanja', 'type' => 'EX', 'balance' => 0],
];

echo "═══════════════════════════════════════════════════════════════\n";
echo " SAMPLE DATA\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📊 Users:\n";
foreach ($mockUsers as $user) {
    echo "   • User {$user['id']}: {$user['name']} ({$user['email']})\n";
}

echo "\n📁 Financial Accounts:\n";
foreach ($mockAccounts as $acc) {
    $typeLabels = [
        'AS' => 'Asset',
        'IN' => 'Income',
        'EX' => 'Expenses',
        'SP' => 'Spending',
        'LI' => 'Liability'
    ];
    $label = $typeLabels[$acc['type']] ?? $acc['type'];
    echo "   • {$acc['name']} - Type: {$acc['type']} ({$label})\n";
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo " QUERY EXAMPLES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Test 1: Semua accounts untuk user
echo "1️⃣  Query: All accounts for User ID 1\n";
echo "────────────────────────────────────────────────────────────\n";
echo "SQL:\n";
echo "SELECT fa.id, fa.name, fa.type, ufa.balance\n";
echo "FROM financial_accounts fa\n";
echo "INNER JOIN user_financial_accounts ufa ON fa.id = ufa.financial_account_id\n";
echo "WHERE ufa.user_id = 1 AND fa.is_active = 1;\n\n";
echo "Result:\n";
foreach ($mockUserAccounts as $acc) {
    echo sprintf("   %-3d | %-20s | %-4s | %s\n", 
        $acc['account_id'], 
        $acc['account_name'], 
        $acc['type'],
        'Rp ' . number_format($acc['balance'], 0, ',', '.')
    );
}
echo "   Total: " . count($mockUserAccounts) . " accounts\n\n";

// Test 2: Assets only
echo "2️⃣  Query: Assets (AS) for User ID 1\n";
echo "────────────────────────────────────────────────────────────\n";
echo "SQL:\n";
echo "SELECT fa.id, fa.name, fa.type, ufa.balance\n";
echo "FROM financial_accounts fa\n";
echo "INNER JOIN user_financial_accounts ufa ON fa.id = ufa.financial_account_id\n";
echo "WHERE ufa.user_id = 1 AND fa.type = 'AS' AND fa.is_active = 1;\n\n";
echo "Result:\n";
$filtered = array_filter($mockUserAccounts, fn($a) => $a['type'] === 'AS');
foreach ($filtered as $acc) {
    echo sprintf("   %-3d | %-20s | %-4s | %s\n", 
        $acc['account_id'], 
        $acc['account_name'], 
        $acc['type'],
        'Rp ' . number_format($acc['balance'], 0, ',', '.')
    );
}
echo "   Total: " . count($filtered) . " assets\n\n";

// Test 3: Income only
echo "3️⃣  Query: Income (IN) for User ID 1\n";
echo "────────────────────────────────────────────────────────────\n";
echo "SQL:\n";
echo "SELECT fa.id, fa.name, fa.type, ufa.balance\n";
echo "FROM financial_accounts fa\n";
echo "INNER JOIN user_financial_accounts ufa ON fa.id = ufa.financial_account_id\n";
echo "WHERE ufa.user_id = 1 AND fa.type = 'IN' AND fa.is_active = 1;\n\n";
echo "Result:\n";
$filtered = array_filter($mockUserAccounts, fn($a) => $a['type'] === 'IN');
foreach ($filtered as $acc) {
    echo sprintf("   %-3d | %-20s | %-4s | %s\n", 
        $acc['account_id'], 
        $acc['account_name'], 
        $acc['type'],
        'Rp ' . number_format($acc['balance'], 0, ',', '.')
    );
}
echo "   Total: " . count($filtered) . " income accounts\n\n";

// Test 4: Multiple types
echo "4️⃣  Query: Assets & Income (AS, IN) for User ID 1\n";
echo "────────────────────────────────────────────────────────────\n";
echo "SQL:\n";
echo "SELECT fa.id, fa.name, fa.type, ufa.balance\n";
echo "FROM financial_accounts fa\n";
echo "INNER JOIN user_financial_accounts ufa ON fa.id = ufa.financial_account_id\n";
echo "WHERE ufa.user_id = 1 AND fa.type IN ('AS', 'IN') AND fa.is_active = 1;\n\n";
echo "Result:\n";
$filtered = array_filter($mockUserAccounts, fn($a) => in_array($a['type'], ['AS', 'IN']));
foreach ($filtered as $acc) {
    echo sprintf("   %-3d | %-20s | %-4s | %s\n", 
        $acc['account_id'], 
        $acc['account_name'], 
        $acc['type'],
        'Rp ' . number_format($acc['balance'], 0, ',', '.')
    );
}
echo "   Total: " . count($filtered) . " accounts\n\n";

// Test 5: Summary by type
echo "5️⃣  Query: Summary by Type for User ID 1\n";
echo "────────────────────────────────────────────────────────────\n";
echo "SQL:\n";
echo "SELECT fa.type, COUNT(*) as count, SUM(ufa.balance) as total\n";
echo "FROM financial_accounts fa\n";
echo "INNER JOIN user_financial_accounts ufa ON fa.id = ufa.financial_account_id\n";
echo "WHERE ufa.user_id = 1 AND fa.is_active = 1\n";
echo "GROUP BY fa.type;\n\n";
echo "Result:\n";

$summary = [];
foreach ($mockUserAccounts as $acc) {
    if (!isset($summary[$acc['type']])) {
        $summary[$acc['type']] = ['count' => 0, 'total' => 0];
    }
    $summary[$acc['type']]['count']++;
    $summary[$acc['type']]['total'] += $acc['balance'];
}

$typeLabels = [
    'AS' => 'Asset',
    'IN' => 'Income',
    'EX' => 'Expenses',
    'SP' => 'Spending',
    'LI' => 'Liability'
];

foreach ($summary as $type => $data) {
    $label = $typeLabels[$type] ?? $type;
    echo sprintf("   %-15s | %d accounts | Total: %s\n", 
        "$label ($type)",
        $data['count'],
        'Rp ' . number_format($data['total'], 0, ',', '.')
    );
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo " ELOQUENT USAGE\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📝 Contoh penggunaan Eloquent:\n\n";

echo "// 1. Semua accounts untuk user\n";
echo "\$accounts = FinancialAccount::forUser(1)->active()->get();\n\n";

echo "// 2. Assets untuk user\n";
echo "\$assets = FinancialAccount::forUser(1)->byType('AS')->get();\n\n";

echo "// 3. Multiple types\n";
echo "\$multiple = FinancialAccount::forUser(1)->byType(['AS', 'IN'])->get();\n\n";

echo "// 4. Dengan balance user\n";
echo "\$accounts = FinancialAccount::forUser(1)->byType('AS')\n";
echo "    ->with(['userFinancialAccounts' => function(\$q) {\n";
echo "        \$q->where('user_id', 1);\n";
echo "    }])->get();\n\n";

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo " API ENDPOINT\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "🌐 API Endpoint:\n";
echo "   GET /api/financial-account/filter/by-user\n\n";

echo "📋 Parameters:\n";
echo "   • user_id (required)  : ID user\n";
echo "   • type (optional)     : AS, IN, EX, SP, LI atau comma-separated\n\n";

echo "📌 Examples:\n";
echo "   • /api/financial-account/filter/by-user?user_id=1\n";
echo "   • /api/financial-account/filter/by-user?user_id=1&type=AS\n";
echo "   • /api/financial-account/filter/by-user?user_id=1&type=AS,IN\n\n";

echo "📊 Response Format:\n";
$sampleResponse = [
    'success' => true,
    'message' => 'Financial accounts retrieved successfully',
    'count' => 2,
    'data' => [
        [
            'id' => 1,
            'name' => 'Kas',
            'type' => 'AS',
            'type_label' => 'Asset (Aset)',
            'is_active' => true,
            'user_balance' => 5000000,
        ],
        [
            'id' => 2,
            'name' => 'Bank BCA',
            'type' => 'AS',
            'type_label' => 'Asset (Aset)',
            'is_active' => true,
            'user_balance' => 15000000,
        ]
    ]
];
echo json_encode($sampleResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

echo "\n\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo " ACCOUNT TYPES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$types = [
    'AS' => 'Asset (Aset)',
    'IN' => 'Income (Pendapatan)',
    'EX' => 'Expenses (Pengeluaran)',
    'SP' => 'Spending (Belanja)',
    'LI' => 'Liability (Kewajiban)'
];

foreach ($types as $code => $label) {
    echo "   • $code = $label\n";
}

echo "\n";
echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║                    ✅ DEMO COMPLETED                          ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n";
echo "\n";
