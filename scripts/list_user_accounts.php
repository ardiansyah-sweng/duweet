<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('user_accounts')->select('id', 'username', 'email', 'created_at')->get();
echo json_encode($rows, JSON_PRETTY_PRINT) . PHP_EOL;
