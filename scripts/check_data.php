<?php
// Simple script to bootstrap Laravel and print counts for debugging seeder output
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "users: " . DB::table('users')->count() . PHP_EOL;
    echo "accounts: " . DB::table('accounts')->count() . PHP_EOL;
    echo "user_account_totals: " . DB::table('user_account_totals')->count() . PHP_EOL;

    $rows = DB::table('user_account_totals')->get();
    foreach ($rows as $r) {
        echo json_encode((array) $r) . PHP_EOL;
    }
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
