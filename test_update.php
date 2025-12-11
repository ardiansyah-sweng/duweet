<?php
// Test Update User - Ganti Nama
// Usage: php test_update.php {user_id} "Nama Baru"

$userId = $argv[1] ?? 1;
$newName = $argv[2] ?? 'John Doe Updated';

$url = "http://localhost:8000/api/user/{$userId}";

$data = [
    'name' => $newName,
    // Opsional: bisa tambah field lain
    // 'first_name' => 'John',
    // 'last_name' => 'Doe',
    // 'email' => 'newemail@example.com',
];

echo "Updating user ID: $userId\n";
echo "New name: $newName\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo json_encode(json_decode($response), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "\n";
