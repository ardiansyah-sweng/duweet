<?php


require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$onlyActive = $argv[1] ?? '1';
$results = User::countFinancialAccountsPerUser($onlyActive === '1');

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
