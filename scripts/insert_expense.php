<?php
// Simple script to insert a test expense into the SQLite database used by the app.
$path = realpath(__DIR__ . '/../database/database.sqlite') ?: __DIR__ . '/../database/database.sqlite';
if (!file_exists($path)) {
    echo "SQLite DB not found: $path\n";
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $fa = $pdo->query("SELECT id FROM financial_accounts WHERE type = 'EX' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (! $fa) {
        echo "No expense financial account (type='EX') found.\n";
        exit(1);
    }
    $ua = $pdo->query("SELECT id FROM user_accounts LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (! $ua) {
        echo "No user_account found.\n";
        exit(1);
    }

    // Generate a 36-char group id (not strictly RFC UUID but fine)
    $gid = substr(str_replace('-', '', uniqid('', true)), 0, 36);

    $sql = 'INSERT INTO transactions (transaction_group_id, user_account_id, financial_account_id, entry_type, amount, balance_effect, description, is_balance, created_at, updated_at) VALUES (:gid, :ua, :fa, :etype, :amt, :beffect, :desc, :isbal, :ca, :ua2)';
    $stmt = $pdo->prepare($sql);
    $params = [
        ':gid' => $gid,
        ':ua' => $ua['id'],
        ':fa' => $fa['id'],
        ':etype' => 'debit',
        ':amt' => 250000,
        ':beffect' => 'decrease',
        ':desc' => 'Test expense insert via script',
        ':isbal' => 1,
        ':ca' => date('Y-m-d H:i:s', strtotime('2025-12-01')),
        ':ua2' => date('Y-m-d H:i:s', strtotime('2025-12-01')),
    ];
    $stmt->execute($params);

    echo "Inserted test expense. user_account_id={$ua['id']} financial_account_id={$fa['id']}\n";
    // Show the newly inserted row
    $last = $pdo->query('SELECT id, transaction_group_id, user_account_id, financial_account_id, amount, balance_effect, created_at FROM transactions ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    echo "Last row: " . json_encode($last) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
