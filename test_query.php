<?php
// run untuk test query tanpa harus lewat terminal atau postman

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UserAccount;

echo "========================================\n";
echo "TEST: Mencari User Berdasarkan Email\n";
echo "========================================\n\n";

// Test 1: Cari user yang ada
$email1 = 'resetme@example.com';
echo "1. Mencari user dengan email: {$email1}\n";
$user1 = UserAccount::cariUserByEmail($email1);

if ($user1) {
    echo "   ✓ User ditemukan!\n";
    echo "   - Username: {$user1->username}\n";
    echo "   - Email: {$user1->email}\n";
    echo "   - Is Active: " . ($user1->is_active ? 'Yes' : 'No') . "\n";
    echo "   - Verified: " . ($user1->verified_at ? 'Yes' : 'No') . "\n\n";
} else {
    echo "   ✗ User tidak ditemukan\n\n";
}

// Test 2: Cari user yang tidak ada
$email2 = 'notfound@example.com';
echo "2. Mencari user dengan email: {$email2}\n";
$user2 = UserAccount::cariUserByEmail($email2);

if ($user2) {
    echo "   ✓ User ditemukan: {$user2->username}\n\n";
} else {
    echo "   ✓ Benar, user tidak ditemukan (expected)\n\n";
}

// Test 3: Cari user lain yang ada
$email3 = 'test@example.com';
echo "3. Mencari user dengan email: {$email3}\n";
$user3 = UserAccount::cariUserByEmail($email3);

if ($user3) {
    echo "   ✓ User ditemukan!\n";
    echo "   - Username: {$user3->username}\n";
    echo "   - Email: {$user3->email}\n";
    echo "   - Is Active: " . ($user3->is_active ? 'Yes' : 'No') . "\n\n";
} else {
    echo "   ✗ User tidak ditemukan\n\n";
}

echo "========================================\n";
echo "TEST: Reset Password Berdasarkan Email\n";
echo "========================================\n\n";

// Test 4: Reset password user yang ada
$emailReset = 'resetme@example.com';
$newPassword = 'NewSecurePass123!';

echo "4. Reset password untuk email: {$emailReset}\n";
echo "   Password baru: {$newPassword}\n";

// Ambil password hash lama dulu
$userBefore = UserAccount::cariUserByEmail($emailReset);
$oldHash = $userBefore ? $userBefore->password : null;
echo "   Hash lama: " . substr($oldHash, 0, 30) . "...\n";

// Reset password
$updated = UserAccount::resetPasswordByEmail($emailReset, $newPassword);

if ($updated) {
    echo "   ✓ Password berhasil direset!\n";

    // Verify password berubah
    $userAfter = UserAccount::cariUserByEmail($emailReset);
    $newHash = $userAfter->password;
    echo "   Hash baru: " . substr($newHash, 0, 30) . "...\n";

    if ($oldHash !== $newHash) {
        echo "   ✓ Password hash berubah (VERIFIED)\n";
    } else {
        echo "   ✗ Password hash TIDAK berubah (ERROR)\n";
    }

    // Test verify password dengan bcrypt
    if (password_verify($newPassword, $newHash)) {
        echo "   ✓ Password baru dapat diverifikasi dengan password_verify\n\n";
    } else {
        echo "   ✗ Password baru GAGAL diverifikasi\n\n";
    }
} else {
    echo "   ✗ Gagal reset password\n\n";
}

// Test 5: Reset password untuk email yang tidak ada
$emailNotExist = 'nobody@example.com';
echo "5. Reset password untuk email yang tidak ada: {$emailNotExist}\n";
$result = UserAccount::resetPasswordByEmail($emailNotExist, 'test123');

if ($result) {
    echo "   ✗ ERROR: Harusnya return false\n\n";
} else {
    echo "   ✓ Benar, return false untuk email tidak ditemukan\n\n";
}

echo "========================================\n";
echo "SEMUA TEST SELESAI!\n";
echo "========================================\n";
