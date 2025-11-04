<?php
/**
 * Usage (PowerShell):
 * php scripts/create_user_account.php --user-name="Name" --user-email="user@example.com" --username="uname" --email="acc@example.com" --password="secret123"
 */

declare(strict_types=1);

chdir(__DIR__ . '/..'); // ensure project root

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\UserAccount;
use App\Constants\UserAccountColumns;

// Parse CLI options
// CLI options - supports single-create (old flags) and batch-create via prefixes
$opts = getopt('', [
    'user-name::',       // single user name or prefix for batch
    'user-email:',       // single user email (for single create)
    'user-email-prefix::',
    'email:',            // single account email (for single create)
    'email-domain::',    // domain used with prefixes
    'username:',         // single username (for single create)
    'username-prefix::',
    'password:',         // single password (for single create)
    'password-prefix::',
    'count::',
    'start-index::',
    'first-name::',
    'middle-name::',
    'last-name::',
    'dob::',
    'verified::',
]);

function usage(): void
{
    echo "Usage: php scripts/create_user_account.php --user-name=\"Name\" --user-email=\"user@example.com\" --username=\"uname\" --email=\"acc@example.com\" --password=\"secret123\"\n";
    exit(1);
}

// Determine mode: single create (default) or batch create when count > 1
$count = isset($opts['count']) ? max(1, (int)$opts['count']) : 1;
$startIndex = isset($opts['start-index']) ? max(1, (int)$opts['start-index']) : 1;

if ($count === 1) {
    // single-create mode: require explicit values
    if (!isset($opts['user-email']) || !isset($opts['username']) || !isset($opts['email']) || !isset($opts['password'])) {
        usage();
    }

    $userName = $opts['user-name'] ?? 'CLI User';
    $userEmail = $opts['user-email'];
    $username = $opts['username'];
    $accountEmail = $opts['email'];
    $password = $opts['password'];
    // Optional PRD fields for single-create
    $firstName = $opts['first-name'] ?? null;
    $middleName = $opts['middle-name'] ?? null;
    $lastName = $opts['last-name'] ?? null;
    $dob = $opts['dob'] ?? null; // expected YYYY-MM-DD
    $verifiedFlag = isset($opts['verified']);
} else {
    // batch-create mode: use prefixes (fallback defaults provided)
    $userNamePrefix = $opts['user-name'] ?? ($opts['user-name-prefix'] ?? 'CLI User');
    $userEmailPrefix = $opts['user-email-prefix'] ?? ($opts['user-email'] ?? 'cli-user');
    $emailDomain = $opts['email-domain'] ?? 'example.com';
    $usernamePrefix = $opts['username-prefix'] ?? ($opts['username'] ?? 'cli_acc');
    $passwordPrefix = $opts['password-prefix'] ?? ($opts['password'] ?? 'pass');

    // We'll generate values inside the loop below
}

try {
    // Batch or single create loop
    $created = [];

    for ($i = 0; $i < $count; $i++) {
        $idx = $startIndex + $i;

        if ($count === 1) {
            // single-create values already set above
            $cuName = $userName;
            $cuEmail = $userEmail;
            $cUsername = $username;
            $cAccountEmail = $accountEmail;
            $cPassword = $password;
            $cFirstName = $firstName ?? null;
            $cMiddleName = $middleName ?? null;
            $cLastName = $lastName ?? null;
            $cDob = $dob ?? null;
            $cVerified = $verifiedFlag ?? false;
        } else {
            $cuName = trim("{$userNamePrefix} {$idx}");
            $cuEmail = "{$userEmailPrefix}{$idx}@{$emailDomain}";
            $cUsername = "{$usernamePrefix}{$idx}";
            $cAccountEmail = "{$usernamePrefix}{$idx}@{$emailDomain}"; // keep account email based on username for uniqueness
            $cPassword = "{$passwordPrefix}{$idx}";
            $cFirstName = null;
            $cMiddleName = null;
            $cLastName = null;
            $cDob = null;
            $cVerified = false;
        }

        // Use transaction per record to avoid partial creations
        try {
            \DB::beginTransaction();

            // Find or create user by email
            $user = User::where('email', $cuEmail)->first();
            if (!$user) {
                // compute usia if dob provided
                $usia = null;
                if ($cDob) {
                    try {
                        $dt = new DateTime($cDob);
                        $now = new DateTime();
                        $usia = $now->diff($dt)->y;
                    } catch (Exception $e) {
                        $usia = null;
                    }
                }

                $user = User::create([
                    'name' => $cuName,
                    'first_name' => $cFirstName,
                    'middle_name' => $cMiddleName,
                    'last_name' => $cLastName,
                    'tanggal_lahir' => $cDob ? (int)$dt->format('d') : null,
                    'bulan_lahir' => $cDob ? (int)$dt->format('m') : null,
                    'tahun_lahir' => $cDob ? (int)$dt->format('Y') : null,
                    'usia' => $usia,
                    'email' => $cuEmail,
                    'password' => bcrypt($cPassword),
                ]);
                echo "Created user id={$user->id} ({$user->email})\n";
            } else {
                echo "Found existing user id={$user->id} ({$user->email})\n";
            }

            // Ensure username/email for UserAccount are unique; if collision, append suffix
            $finalUsername = $cUsername;
            $suffix = 0;
            while (UserAccount::where(UserAccountColumns::USERNAME, $finalUsername)->exists()) {
                $suffix++;
                $finalUsername = $cUsername . '_' . $suffix;
            }

            $finalAccountEmail = $cAccountEmail;
            $suffix = 0;
            while (UserAccount::where(UserAccountColumns::EMAIL, $finalAccountEmail)->exists()) {
                $suffix++;
                $local = preg_replace('/@.*$/', '', $cAccountEmail);
                $finalAccountEmail = $local . '+' . $suffix . '@' . $emailDomain;
            }

            $uaPayload = [
                UserAccountColumns::ID_USER => $user->id,
                UserAccountColumns::USERNAME => $finalUsername,
                UserAccountColumns::EMAIL => $finalAccountEmail,
                UserAccountColumns::PASSWORD => bcrypt($cPassword),
                UserAccountColumns::IS_ACTIVE => true,
            ];
            if (!empty($cVerified)) {
                $uaPayload[UserAccountColumns::VERIFIED_AT] = now();
            }

            $ua = UserAccount::create($uaPayload);

            \DB::commit();

            echo "Created UserAccount id={$ua->id} username={$ua->{UserAccountColumns::USERNAME}} email={$ua->{UserAccountColumns::EMAIL}}\n";
            $created[] = $ua->id;
        } catch (\Exception $e) {
            \DB::rollBack();
            echo "Error creating account for index {$idx}: " . $e->getMessage() . "\n";
        }
    }

    echo "Batch create finished. Created user_account ids: " . implode(', ', $created) . "\n";
    exit(0);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
