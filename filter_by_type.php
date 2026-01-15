<?php
/**
 * Query Filter: Financial Account by Account Type
 * Filter account berdasarkan tipe: AS, IN, EX, SP, LI
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║      QUERY FILTER: Financial Account by Account Type              ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Mock data
$allAccounts = [
    // Assets (AS)
    ['id' => 1, 'name' => 'Kas', 'type' => 'AS', 'balance' => 5000000, 'user_id' => 1],
    ['id' => 2, 'name' => 'Bank BCA', 'type' => 'AS', 'balance' => 15000000, 'user_id' => 1],
    ['id' => 3, 'name' => 'Bank Mandiri', 'type' => 'AS', 'balance' => 8000000, 'user_id' => 1],
    ['id' => 4, 'name' => 'Deposito', 'type' => 'AS', 'balance' => 50000000, 'user_id' => 1],
    
    // Income (IN)
    ['id' => 5, 'name' => 'Gaji', 'type' => 'IN', 'balance' => 0, 'user_id' => 1],
    ['id' => 6, 'name' => 'Bonus', 'type' => 'IN', 'balance' => 0, 'user_id' => 1],
    ['id' => 7, 'name' => 'Investasi', 'type' => 'IN', 'balance' => 0, 'user_id' => 1],
    
    // Expenses (EX)
    ['id' => 8, 'name' => 'Biaya Listrik', 'type' => 'EX', 'balance' => 0, 'user_id' => 1],
    ['id' => 9, 'name' => 'Biaya Air', 'type' => 'EX', 'balance' => 0, 'user_id' => 1],
    ['id' => 10, 'name' => 'Biaya Internet', 'type' => 'EX', 'balance' => 0, 'user_id' => 1],
    
    // Spending (SP)
    ['id' => 11, 'name' => 'Belanja Harian', 'type' => 'SP', 'balance' => 0, 'user_id' => 1],
    ['id' => 12, 'name' => 'Transportasi', 'type' => 'SP', 'balance' => 0, 'user_id' => 1],
    
    // Liability (LI)
    ['id' => 13, 'name' => 'Hutang KPR', 'type' => 'LI', 'balance' => 0, 'user_id' => 1],
    ['id' => 14, 'name' => 'Hutang Kartu Kredit', 'type' => 'LI', 'balance' => 0, 'user_id' => 1],
];

$typeLabels = [
    'AS' => 'Asset (Aset)',
    'IN' => 'Income (Pendapatan)',
    'EX' => 'Expenses (Pengeluaran)',
    'SP' => 'Spending (Belanja)',
    'LI' => 'Liability (Kewajiban)'
];

$userId = 1;

echo "User ID: $userId\n";
echo "Total Accounts: " . count($allAccounts) . "\n";
echo "\n";

// Function untuk display accounts
function displayAccounts($accounts, $title, $type) {
    global $typeLabels;
    
    echo "╔════════════════════════════════════════════════════════════════════╗\n";
    echo "║  $title\n";
    echo "╚════════════════════════════════════════════════════════════════════╝\n\n";
    
    echo "📝 SQL Query:\n";
    echo "────────────────────────────────────────────────────────────────────\n";
    if (is_array($type)) {
        $typeList = implode("', '", $type);
        echo "SELECT fa.id, fa.name, fa.type, ufa.balance\n";
        echo "FROM financial_accounts fa\n";
        echo "INNER JOIN user_financial_accounts ufa ON fa.id = ufa.financial_account_id\n";
        echo "WHERE ufa.user_id = 1\n";
        echo "  AND fa.type IN ('$typeList')\n";
        echo "  AND fa.is_active = 1;\n\n";
    } else {
        echo "SELECT fa.id, fa.name, fa.type, ufa.balance\n";
        echo "FROM financial_accounts fa\n";
        echo "INNER JOIN user_financial_accounts ufa ON fa.id = ufa.financial_account_id\n";
        echo "WHERE ufa.user_id = 1\n";
        echo "  AND fa.type = '$type'\n";
        echo "  AND fa.is_active = 1;\n\n";
    }
    
    echo "📊 Result:\n";
    echo "────────────────────────────────────────────────────────────────────\n";
    
    if (empty($accounts)) {
        echo "   ⚠️  No accounts found\n\n";
        return;
    }
    
    echo sprintf("   %-4s | %-30s | %-4s | %s\n", "ID", "Account Name", "Type", "Balance");
    echo "   " . str_repeat("─", 68) . "\n";
    
    $totalBalance = 0;
    foreach ($accounts as $acc) {
        echo sprintf("   %-4d | %-30s | %-4s | %s\n", 
            $acc['id'], 
            $acc['name'], 
            $acc['type'],
            'Rp ' . number_format($acc['balance'], 0, ',', '.')
        );
        $totalBalance += $acc['balance'];
    }
    
    echo "   " . str_repeat("─", 68) . "\n";
    echo sprintf("   %-4s | %-30s | %-4s | %s\n", 
        "", 
        "TOTAL: " . count($accounts) . " accounts", 
        "",
        'Rp ' . number_format($totalBalance, 0, ',', '.')
    );
    echo "\n";
}

// Filter per type
foreach ($typeLabels as $code => $label) {
    $filtered = array_filter($allAccounts, fn($a) => $a['type'] === $code);
    displayAccounts($filtered, "Filter: $label ($code)", $code);
}

// Multiple types
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║  COMBINED FILTERS (Multiple Types)\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

// Assets + Income
$filtered = array_filter($allAccounts, fn($a) => in_array($a['type'], ['AS', 'IN']));
displayAccounts($filtered, "Filter: Assets + Income (AS, IN)", ['AS', 'IN']);

// Expenses + Spending
$filtered = array_filter($allAccounts, fn($a) => in_array($a['type'], ['EX', 'SP']));
displayAccounts($filtered, "Filter: Expenses + Spending (EX, SP)", ['EX', 'SP']);

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║  ELOQUENT QUERY EXAMPLES\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

foreach ($typeLabels as $code => $label) {
    echo "// Filter: $label\n";
    echo "\$accounts = FinancialAccount::forUser(\$userId)\n";
    echo "    ->byType('$code')\n";
    echo "    ->active()\n";
    echo "    ->get();\n\n";
}

echo "// Multiple types\n";
echo "\$accounts = FinancialAccount::forUser(\$userId)\n";
echo "    ->byType(['AS', 'IN'])\n";
echo "    ->active()\n";
echo "    ->get();\n\n";

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║  API ENDPOINT EXAMPLES\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

$baseUrl = "http://localhost:8000/api/financial-account/filter/by-user";

foreach ($typeLabels as $code => $label) {
    echo "// $label\n";
    echo "GET $baseUrl?user_id=1&type=$code\n\n";
}

echo "// Multiple types\n";
echo "GET $baseUrl?user_id=1&type=AS,IN\n";
echo "GET $baseUrl?user_id=1&type=EX,SP\n";
echo "GET $baseUrl?user_id=1&type=AS,IN,EX\n\n";

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║  SUMMARY BY TYPE\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

echo "📊 Distribution:\n";
echo "────────────────────────────────────────────────────────────────────\n";
echo sprintf("   %-30s | %-10s | %s\n", "Type", "Count", "Total Balance");
echo "   " . str_repeat("─", 68) . "\n";

foreach ($typeLabels as $code => $label) {
    $filtered = array_filter($allAccounts, fn($a) => $a['type'] === $code);
    $total = array_sum(array_column($filtered, 'balance'));
    echo sprintf("   %-30s | %-10d | %s\n", 
        $label,
        count($filtered),
        'Rp ' . number_format($total, 0, ',', '.')
    );
}

echo "   " . str_repeat("─", 68) . "\n";
$grandTotal = array_sum(array_column($allAccounts, 'balance'));
echo sprintf("   %-30s | %-10d | %s\n", 
    "GRAND TOTAL",
    count($allAccounts),
    'Rp ' . number_format($grandTotal, 0, ',', '.')
);

echo "\n\n";
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║                  ✅ QUERY FILTER COMPLETED                         ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n";
echo "\n";
