<?php
// Test script untuk API User Search
// Jalankan: php test_search.php

// Ambil sample user untuk testing
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Sample User Data ===\n\n";

$users = App\Models\User::take(5)->get();

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "First Name: {$user->first_name}\n";
    echo "Last Name: {$user->last_name}\n";
    echo "Provinsi: {$user->provinsi}\n";
    echo "---\n\n";
}

echo "\n=== Test Search Endpoint ===\n";
echo "Gunakan salah satu query ini:\n\n";

echo "curl http://localhost:8000/api/user/search?q=" . urlencode($users->first()->name) . "\n";
echo "curl http://localhost:8000/api/user/search?q=" . urlencode($users->first()->email) . "\n";

if ($users->first()->provinsi) {
    echo "curl http://localhost:8000/api/user/search?q=" . urlencode($users->first()->provinsi) . "\n";
}
