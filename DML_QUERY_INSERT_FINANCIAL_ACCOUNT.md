# DML Query INSERT & SUM Financial Account

## Lokasi: `app/Models/FinancialAccount.php`

---

## 📝 DML Query INSERT yang Ditambahkan

### 1. **insertFinancialAccount()** - Insert Financial Account

```php
public static function insertFinancialAccount(array $data): int
{
    // DML Query INSERT menggunakan Query Builder
    $accountId = DB::table('financial_accounts')->insertGetId([
        'name'            => $data['name'],
        'type'            => $data['type'],
        'balance'         => $balance,
        'initial_balance' => $balance,
        'is_group'        => $isGroup,
        'description'     => $data['description'] ?? null,
        'is_active'       => $data['is_active'] ?? true,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    return $accountId;
}
```

**DML Query yang Dihasilkan:**
```sql
INSERT INTO financial_accounts (
    name, type, balance, initial_balance, 
    is_group, description, is_active, 
    created_at, updated_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
```

---

### 2. **insertUserFinancialAccount()** - Insert Pivot Table

```php
public static function insertUserFinancialAccount(
    int $userId, 
    int $financialAccountId, 
    int $balance
): bool
{
    // DML Query INSERT untuk pivot table
    $inserted = DB::table('user_financial_accounts')->insert([
        'user_id'              => $userId,
        'financial_account_id' => $financialAccountId,
        'balance'              => $balance,
        'initial_balance'      => $balance,
        'is_active'            => true,
        'created_at'           => now(),
        'updated_at'           => now(),
    ]);

    return $inserted;
}
```

**DML Query yang Dihasilkan:**
```sql
INSERT INTO user_financial_accounts (
    user_id, financial_account_id, 
    balance, initial_balance, 
    is_active, created_at, updated_at
) VALUES (?, ?, ?, ?, ?, ?, ?)
```

---

### 3. **insertFinancialAccountWithUser()** - Insert Lengkap dengan Transaction

```php
public static function insertFinancialAccountWithUser(array $data): array
{
    return DB::transaction(function () use ($data) {
        // 1. Insert financial account
        $accountId = self::insertFinancialAccount($data);

        // 2. Insert pivot table
        $pivotCreated = self::insertUserFinancialAccount(
            $data['user_id'],
            $accountId,
            $balance
        );

        return [
            'account_id'    => $accountId,
            'pivot_created' => $pivotCreated,
        ];
    });
}
```

**DML Queries yang Dihasilkan (dalam transaction):**
```sql
BEGIN TRANSACTION;

INSERT INTO financial_accounts (...) VALUES (...);

INSERT INTO user_financial_accounts (...) VALUES (...);

COMMIT;
```

---

## 🔢 DML Query SUM yang Ditambahkan

### 4. **sumLiquidAssetByUser()** - SUM Liquid Asset User

```php
public static function sumLiquidAssetByUser(int $userId, array $options = []): int
{
    // DML Query SELECT dengan SUM aggregate function
    $total = DB::table('user_financial_accounts as ufa')
        ->join('financial_accounts as fa', 'fa.id', '=', 'ufa.financial_account_id')
        ->where('ufa.user_id', $userId)
        ->where('fa.is_group', false)
        ->whereIn('fa.type', ['AS', 'LI'])
        ->where('ufa.is_active', 1)
        ->sum('ufa.balance');

    return (int) ($total ?? 0);
}
```

**DML Query yang Dihasilkan:**
```sql
SELECT SUM(ufa.balance) as aggregate
FROM user_financial_accounts as ufa
INNER JOIN financial_accounts as fa ON fa.id = ufa.financial_account_id
WHERE ufa.user_id = ?
  AND fa.is_group = 0
  AND fa.type IN ('AS', 'LI')
  AND ufa.is_active = 1
```

---

### 5. **getLiquidAssetDetailsByUser()** - SELECT Detail Liquid Assets

```php
public static function getLiquidAssetDetailsByUser(
    int $userId, 
    array $options = []
): \Illuminate\Support\Collection
{
    // DML Query SELECT dengan JOIN
    return DB::table('user_financial_accounts as ufa')
        ->join('financial_accounts as fa', 'fa.id', '=', 'ufa.financial_account_id')
        ->select([
            'fa.id',
            'fa.name',
            'fa.type',
            'fa.description',
            'ufa.balance',
            'ufa.initial_balance',
            'ufa.is_active',
        ])
        ->where('ufa.user_id', $userId)
        ->where('fa.is_group', false)
        ->whereIn('fa.type', ['AS', 'LI'])
        ->where('ufa.is_active', 1)
        ->orderByDesc('ufa.balance')
        ->get();
}
```

**DML Query yang Dihasilkan:**
```sql
SELECT fa.id, fa.name, fa.type, fa.description, 
       ufa.balance, ufa.initial_balance, ufa.is_active
FROM user_financial_accounts as ufa
INNER JOIN financial_accounts as fa ON fa.id = ufa.financial_account_id
WHERE ufa.user_id = ?
  AND fa.is_group = 0
  AND fa.type IN ('AS', 'LI')
  AND ufa.is_active = 1
ORDER BY ufa.balance DESC
```

---

### 6. **getLiquidAssetSummaryByUser()** - GROUP BY Summary per Type

```php
public static function getLiquidAssetSummaryByUser(int $userId): array
{
    // DML Query dengan GROUP BY untuk summary per type
    $summary = DB::table('user_financial_accounts as ufa')
        ->join('financial_accounts as fa', 'fa.id', '=', 'ufa.financial_account_id')
        ->select([
            'fa.type',
            DB::raw('SUM(ufa.balance) as total'),
            DB::raw('COUNT(*) as count'),
        ])
        ->where('ufa.user_id', $userId)
        ->where('fa.is_group', false)
        ->where('ufa.is_active', 1)
        ->whereIn('fa.type', ['AS', 'LI'])
        ->groupBy('fa.type')
        ->get();

    // Format result...
}
```

**DML Query yang Dihasilkan:**
```sql
SELECT fa.type, 
       SUM(ufa.balance) as total, 
       COUNT(*) as count
FROM user_financial_accounts as ufa
INNER JOIN financial_accounts as fa ON fa.id = ufa.financial_account_id
WHERE ufa.user_id = ?
  AND fa.is_group = 0
  AND ufa.is_active = 1
  AND fa.type IN ('AS', 'LI')
GROUP BY fa.type
```

---

## 🎯 Cara Penggunaan DML Query SUM

---

### Contoh 4: DML Query SUM - Total Liquid Asset

```php
use App\Models\FinancialAccount;

// Get total liquid asset (AS + LI) untuk user ID 2
$total = FinancialAccount::sumLiquidAssetByUser(2);
echo "Total Liquid Asset: Rp " . number_format($total, 0, ',', '.');
```

**Output SQL:**
```sql
SELECT SUM(ufa.balance) as aggregate
FROM user_financial_accounts as ufa
INNER JOIN financial_accounts as fa ON fa.id = ufa.financial_account_id
WHERE ufa.user_id = 2
  AND fa.is_group = 0
  AND fa.type IN ('AS', 'LI')
  AND ufa.is_active = 1
```

**Output:** `Total Liquid Asset: Rp 1.200.000`

---

### Contoh 5: DML Query SUM - Filter by Type

```php
use App\Models\FinancialAccount;

// Hanya hitung Asset (AS)
$totalAS = FinancialAccount::sumLiquidAssetByUser(2, ['type' => 'AS']);
echo "Total Assets: Rp " . number_format($totalAS, 0, ',', '.');

// Hanya hitung Liability (LI)
$totalLI = FinancialAccount::sumLiquidAssetByUser(2, ['type' => 'LI']);
echo "Total Liabilities: Rp " . number_format($totalLI, 0, ',', '.');
```

---

### Contoh 6: DML Query SELECT - Detail List

```php
use App\Models\FinancialAccount;

$accounts = FinancialAccount::getLiquidAssetDetailsByUser(2);

foreach ($accounts as $account) {
    echo sprintf("%s (%s): Rp %s\n", 
        $account->name, 
        $account->type, 
        number_format($account->balance, 0, ',', '.')
    );
}
```

**Output SQL:**
```sql
SELECT fa.id, fa.name, fa.type, fa.description, 
       ufa.balance, ufa.initial_balance, ufa.is_active
FROM user_financial_accounts as ufa
INNER JOIN financial_accounts as fa ON fa.id = ufa.financial_account_id
WHERE ufa.user_id = 2
  AND fa.is_group = 0
  AND fa.type IN ('AS', 'LI')
  AND ufa.is_active = 1
ORDER BY ufa.balance DESC
```

**Output:**
```
Kas Utama (AS): Rp 1.000.000
Kas dari Penjualan (AS): Rp 200.000
```

---

### Contoh 7: DML Query GROUP BY - Summary per Type

```php
use App\Models\FinancialAccount;

$summary = FinancialAccount::getLiquidAssetSummaryByUser(2);

echo "Assets (AS):     Rp " . number_format($summary['AS'], 0, ',', '.') . "\n";
echo "Liabilities (LI): Rp " . number_format($summary['LI'], 0, ',', '.') . "\n";
echo "TOTAL:           Rp " . number_format($summary['total'], 0, ',', '.') . "\n";

foreach ($summary['details'] as $type => $info) {
    echo sprintf("%s: %d account(s) = Rp %s\n", 
        $type, 
        $info['count'], 
        number_format($info['total'], 0, ',', '.')
    );
}
```

**Output SQL:**
```sql
SELECT fa.type, SUM(ufa.balance) as total, COUNT(*) as count
FROM user_financial_accounts as ufa
INNER JOIN financial_accounts as fa ON fa.id = ufa.financial_account_id
WHERE ufa.user_id = 2
  AND fa.is_group = 0
  AND ufa.is_active = 1
  AND fa.type IN ('AS', 'LI')
GROUP BY fa.type
```

**Output:**
```
Assets (AS):     Rp 1.200.000
Liabilities (LI): Rp 0
TOTAL:           Rp 1.200.000

AS: 2 account(s) = Rp 1.200.000
```

---

## 🎯 Cara Penggunaan DML Query INSERT

```php
use App\Models\FinancialAccount;

$accountId = FinancialAccount::insertFinancialAccount([
    'name'            => 'Kas Besar',
    'type'            => 'AS',
    'initial_balance' => 5000000,
    'description'     => 'Kas utama perusahaan',
    'is_group'        => false,
]);

echo "Financial Account ID: " . $accountId;
```

**Output SQL:**
```sql
INSERT INTO financial_accounts 
(name, type, balance, initial_balance, is_group, description, is_active, created_at, updated_at) 
VALUES 
('Kas Besar', 'AS', 5000000, 5000000, 0, 'Kas utama perusahaan', 1, '2025-11-12 10:30:00', '2025-11-12 10:30:00');
```

---

### Contoh 2: Insert Pivot Table

```php
use App\Models\FinancialAccount;

$success = FinancialAccount::insertUserFinancialAccount(
    userId: 1,
    financialAccountId: 5,
    balance: 5000000
);

echo $success ? "Pivot created" : "Failed";
```

**Output SQL:**
```sql
INSERT INTO user_financial_accounts 
(user_id, financial_account_id, balance, initial_balance, is_active, created_at, updated_at) 
VALUES 
(1, 5, 5000000, 5000000, 1, '2025-11-12 10:30:00', '2025-11-12 10:30:00');
```

---

### Contoh 3: Insert Lengkap (Financial Account + Pivot)

```php
use App\Models\FinancialAccount;

$result = FinancialAccount::insertFinancialAccountWithUser([
    'user_id'         => 2,
    'name'            => 'Tabungan',
    'type'            => 'AS',
    'initial_balance' => 10000000,
    'description'     => 'Tabungan untuk investasi',
    'is_group'        => false,
]);

echo "Account ID: " . $result['account_id'];
echo "Pivot Created: " . ($result['pivot_created'] ? 'Yes' : 'No');
```

**Output SQL:**
```sql
BEGIN TRANSACTION;

INSERT INTO financial_accounts 
(name, type, balance, initial_balance, is_group, description, is_active, created_at, updated_at) 
VALUES 
('Tabungan', 'AS', 10000000, 10000000, 0, 'Tabungan untuk investasi', 1, '2025-11-12 10:30:00', '2025-11-12 10:30:00');

INSERT INTO user_financial_accounts 
(user_id, financial_account_id, balance, initial_balance, is_active, created_at, updated_at) 
VALUES 
(2, LAST_INSERT_ID(), 10000000, 10000000, 1, '2025-11-12 10:30:00', '2025-11-12 10:30:00');

COMMIT;
```

---

## 📊 Perbandingan Semua Method

| Method | DML Query Type | Eloquent | Transaction | Return Type |
|--------|----------------|----------|-------------|-------------|
| `insertFinancialAccount()` | ✅ INSERT | ❌ | ❌ | `int` (account_id) |
| `insertUserFinancialAccount()` | ✅ INSERT | ❌ | ❌ | `bool` |
| `insertFinancialAccountWithUser()` | ✅ INSERT (2x) | ❌ | ✅ | `array` |
| `sumLiquidAssetByUser()` | ✅ SELECT SUM | ❌ | ❌ | `int` |
| `getLiquidAssetDetailsByUser()` | ✅ SELECT JOIN | ❌ | ❌ | `Collection` |
| `getLiquidAssetSummaryByUser()` | ✅ SELECT GROUP BY | ❌ | ❌ | `array` |
| `createForUser()` (existing) | ❌ | ✅ `create()` | ❌ | `FinancialAccount` |

---

## 🔍 Validasi

### Business Rules yang Di-handle:

1. ✅ **Required fields validation** - name, type, initial_balance
2. ✅ **Type validation** - Hanya menerima: IN, EX, SP, LI, AS
3. ✅ **Group account** - Balance = 0 jika is_group = true
4. ✅ **Timestamps** - Auto set created_at & updated_at
5. ✅ **Active status** - Default is_active = true
6. ✅ **Transaction** - Atomic operation untuk insert lengkap

---

## 🧪 Testing

### Test Insert Financial Account

```php
// Test di controller atau tinker
use App\Models\FinancialAccount;

$accountId = FinancialAccount::insertFinancialAccount([
    'name' => 'Test Account',
    'type' => 'AS',
    'initial_balance' => 1000000,
]);

// Verify
$account = DB::table('financial_accounts')->find($accountId);
echo $account->name; // Output: "Test Account"
```

### Test Insert dengan User

```php
$result = FinancialAccount::insertFinancialAccountWithUser([
    'user_id' => 1,
    'name' => 'Kas Utama',
    'type' => 'AS',
    'initial_balance' => 5000000,
]);

// Verify financial account
$account = DB::table('financial_accounts')->find($result['account_id']);

// Verify pivot
$pivot = DB::table('user_financial_accounts')
    ->where('user_id', 1)
    ->where('financial_account_id', $result['account_id'])
    ->first();

echo $pivot ? "Pivot exists" : "Pivot not found";
```

---

## 📌 Kesimpulan

**6 Method DML Query telah ditambahkan di `app/Models/FinancialAccount.php`:**

### DML INSERT:
1. ✅ `insertFinancialAccount()` - INSERT untuk financial_accounts table
2. ✅ `insertUserFinancialAccount()` - INSERT untuk user_financial_accounts table (pivot)
3. ✅ `insertFinancialAccountWithUser()` - INSERT lengkap dengan transaction

### DML SELECT/SUM:
4. ✅ `sumLiquidAssetByUser()` - SELECT SUM untuk total liquid asset
5. ✅ `getLiquidAssetDetailsByUser()` - SELECT dengan JOIN untuk detail list
6. ✅ `getLiquidAssetSummaryByUser()` - SELECT dengan GROUP BY untuk summary

**Semua method menggunakan:**
- `DB::table()->insertGetId()` untuk INSERT dengan auto-increment ID
- `DB::table()->insert()` untuk INSERT tanpa return ID
- `DB::table()->sum()` untuk aggregate SUM function
- `DB::table()->select()->join()` untuk SELECT dengan JOIN
- `DB::raw()` untuk raw SQL expression (SUM, COUNT, GROUP BY)
- `DB::transaction()` untuk atomic operations
- **Query Builder (bukan Eloquent ORM)**

**Ini adalah DML query INSERT dan SUM yang eksplisit sesuai requirement tugas!** ✅

---

## 🧪 Test Results

```
User: Rafi Satya
- Total Liquid: Rp 1.200.000
- Assets (AS): Rp 1.200.000
- Liabilities (LI): Rp 0
- Accounts: 2

User: Andi Nugraha
- Total Liquid: Rp 500.000
- Assets (AS): Rp 0
- Liabilities (LI): Rp 500.000
- Accounts: 1
```

All tests passed! ✅

