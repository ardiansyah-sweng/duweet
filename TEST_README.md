# 🔍 Test Query Financial Account Filter

File-file test yang sudah dibuat untuk menguji filter Financial Account berdasarkan User dan AccountType:

## 📁 Files yang dibuat:

1. **test_queries.sql** - SQL queries langsung
2. **test_api.ps1** - PowerShell script untuk test API endpoints
3. **test_api.bat** - Batch script untuk test API endpoints
4. **test_filter.php** - PHP script untuk test query Eloquent

---

## 🚀 Cara Menjalankan Test:

### Option 1: SQL Queries (Langsung ke Database)

```bash
# Jalankan query di database client (MySQL/PostgreSQL)
# Buka file: test_queries.sql
# Copy paste query yang ingin ditest

# Contoh query utama:
SELECT 
    fa.id, fa.name, fa.type,
    ufa.balance as user_balance
FROM financial_accounts fa
INNER JOIN user_financial_accounts ufa ON fa.id = ufa.financial_account_id
WHERE ufa.user_id = 1
  AND fa.type = 'AS'
  AND fa.is_active = 1;
```

### Option 2: Test API Endpoint (Setelah Server Running)

#### 2a. Menggunakan PowerShell:
```powershell
# 1. Start Laravel server
php artisan serve

# 2. Di terminal baru, jalankan test
.\test_api.ps1
```

#### 2b. Menggunakan Batch File:
```cmd
# 1. Start Laravel server
php artisan serve

# 2. Di terminal baru, jalankan test
test_api.bat
```

#### 2c. Menggunakan curl manual:
```bash
# Get all accounts untuk user_id=1
curl "http://localhost:8000/api/financial-account/filter/by-user?user_id=1"

# Get Assets (AS) untuk user_id=1
curl "http://localhost:8000/api/financial-account/filter/by-user?user_id=1&type=AS"

# Get Income (IN) untuk user_id=1
curl "http://localhost:8000/api/financial-account/filter/by-user?user_id=1&type=IN"

# Get Multiple types (AS,IN)
curl "http://localhost:8000/api/financial-account/filter/by-user?user_id=1&type=AS,IN"
```

---

## 📊 Query Examples dalam Code:

### Eloquent (di Controller atau Service):
```php
use App\Models\FinancialAccount;

// 1. Semua accounts untuk user
$accounts = FinancialAccount::forUser($userId)->active()->get();

// 2. Assets untuk user
$assets = FinancialAccount::forUser($userId)->byType('AS')->get();

// 3. Multiple types untuk user
$multiple = FinancialAccount::forUser($userId)->byType(['AS', 'IN'])->get();

// 4. Dengan relasi balance user
$accounts = FinancialAccount::forUser($userId)
    ->byType('AS')
    ->with(['userFinancialAccounts' => function($q) use ($userId) {
        $q->where('user_id', $userId);
    }])
    ->get();
```

### Raw SQL (jika diperlukan):
```php
use Illuminate\Support\Facades\DB;

$results = DB::table('financial_accounts as fa')
    ->join('user_financial_accounts as ufa', 'fa.id', '=', 'ufa.financial_account_id')
    ->where('ufa.user_id', $userId)
    ->where('fa.type', 'AS')
    ->where('fa.is_active', true)
    ->select('fa.*', 'ufa.balance as user_balance')
    ->get();
```

---

## 🔧 Setup Requirements:

Sebelum menjalankan test, pastikan:

1. **Database sudah running** dan terisi data
2. **Composer dependencies installed**: `composer install`
3. **.env file** sudah dikonfigurasi dengan benar
4. **Database migration** sudah dijalankan: `php artisan migrate`
5. **Database seeder** sudah dijalankan (jika ada): `php artisan db:seed`

---

## 📋 Account Types:

- **AS** = Asset (Aset)
- **IN** = Income (Pendapatan)
- **EX** = Expenses (Pengeluaran)
- **SP** = Spending (Belanja)
- **LI** = Liability (Kewajiban)

---

## ✅ Expected Response Format:

```json
{
    "success": true,
    "message": "Financial accounts retrieved successfully",
    "count": 2,
    "data": [
        {
            "id": 1,
            "name": "Kas",
            "type": "AS",
            "type_label": "Asset (Aset)",
            "description": "Kas di tangan",
            "is_group": false,
            "is_active": true,
            "user_balance": 1000000,
            "user_initial_balance": 500000
        },
        {
            "id": 2,
            "name": "Bank BCA",
            "type": "AS",
            "type_label": "Asset (Aset)",
            "description": "Rekening BCA",
            "is_group": false,
            "is_active": true,
            "user_balance": 5000000,
            "user_initial_balance": 3000000
        }
    ]
}
```

---

## 🐛 Troubleshooting:

### Error: "Connection refused"
- Pastikan Laravel server running: `php artisan serve`

### Error: "User not found"
- Ganti user_id dengan ID yang ada di database

### Error: "No accounts found"
- Pastikan ada data di tabel `user_financial_accounts`
- Jalankan seeder jika belum ada data

### Error: "vendor/autoload.php not found"
- Jalankan: `composer install`

---

## 🎯 API Endpoint:

**URL**: `GET /api/financial-account/filter/by-user`

**Query Parameters**:
- `user_id` (required): ID user
- `type` (optional): Account type atau comma-separated types (AS, IN, EX, SP, LI)

**Examples**:
- `/api/financial-account/filter/by-user?user_id=1`
- `/api/financial-account/filter/by-user?user_id=1&type=AS`
- `/api/financial-account/filter/by-user?user_id=1&type=AS,IN`
