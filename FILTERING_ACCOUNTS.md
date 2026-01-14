# Panduan Filtering Akun Finansial

## Deskripsi
Sistem ini memungkinkan filtering akun finansial berdasarkan tipe akun dengan berbagai cara yang fleksibel.

## Tipe Akun yang Tersedia

| Kode | Nama | Label |
|------|------|-------|
| IN | INCOME | Pendapatan |
| EX | EXPENSES | Beban |
| SP | SPENDING | Pengeluaran |
| LI | LIABILITY | Kewajiban |
| AS | ASSET | Aset |

## API Endpoints

### 1. Dapatkan Akun Finansial (dengan filter opsional)

**GET** `/api/financial-accounts`

**Query Parameters:**
- `type` (opsional): Tipe akun. Format: `AS` atau multiple `AS,LI,IN`
- `summary` (opsional): Jika `1`, tambahkan summary data

**Contoh:**
```
GET /api/financial-accounts
GET /api/financial-accounts?type=AS
GET /api/financial-accounts?type=AS,LI
GET /api/financial-accounts?type=IN&summary=1
```

**Response:**
```json
{
  "source": "all_accounts",
  "data": [...],
  "count": 5,
  "filter": "AS",
  "summary": [
    {
      "type": "AS",
      "count": 5,
      "total_balance": 1000000
    }
  ]
}
```

### 2. Dapatkan Referensi Tipe Akun

**GET** `/api/financial-accounts/types`

**Response:**
```json
{
  "data": [
    {"value": "IN", "name": "INCOME", "label": "Pendapatan"},
    {"value": "EX", "name": "EXPENSES", "label": "Beban"},
    {"value": "SP", "name": "SPENDING", "label": "Pengeluaran"},
    {"value": "LI", "name": "LIABILITY", "label": "Kewajiban"},
    {"value": "AS", "name": "ASSET", "label": "Aset"}
  ],
  "count": 5
}
```

### 3. Dapatkan Summary Akun

**GET** `/api/financial-accounts/summary`

**Response:**
```json
{
  "source": "all_accounts",
  "data": [
    {
      "type": "AS",
      "count": 5,
      "total_balance": 1000000
    },
    {
      "type": "IN",
      "count": 3,
      "total_balance": 500000
    }
  ]
}
```

### 4. Akun User (Authenticated)

**GET** `/api/user/financial-accounts`

Memerlukan autentikasi (Bearer Token dengan sanctum)

**Query Parameters:**
- `type` (opsional): Tipe akun
- `summary` (opsional): Jika `1`, tambahkan summary

**Response:**
```json
{
  "source": "user_accounts",
  "user_id": 1,
  "data": [...],
  "count": 3
}
```

## Query di Model (Eloquent)

### Query Dasar

```php
use App\Models\FinancialAccount;

// Filter berdasarkan tipe
$assetAccounts = FinancialAccount::ofType('AS')->get();

// Filter multiple tipe
$accounts = FinancialAccount::ofType(['IN', 'EX'])->get();

// Filter dengan string terpisah koma
$accounts = FinancialAccount::ofType('SP,LI')->get();

// Hanya akun aktif
$activeAccounts = FinancialAccount::active()->get();

// Hanya grup akun
$groupAccounts = FinancialAccount::groups()->get();

// Kombinasi filter
$activeAssets = FinancialAccount::ofType('AS')->active()->get();

// Filter aktif berdasarkan tipe
$activeAccounts = FinancialAccount::activeByType('AS')->get();
```

### Query Summary

```php
// Summary per tipe dengan count dan total balance
$summary = FinancialAccount::summaryByType();

// Hasil:
// [
//   {"type": "AS", "count": 5, "total_balance": 1000000},
//   {"type": "IN", "count": 3, "total_balance": 500000}
// ]

// Grouped by type dengan count
$grouped = FinancialAccount::groupedByType();

// Hasil:
// [
//   {"type": "AS", "total": 5},
//   {"type": "IN", "total": 3}
// ]
```

## Query di User Model

```php
use App\Models\User;

$user = User::find(1);

// Dapatkan akun berdasarkan tipe
$accounts = $user->getAccountsByType('AS');

// Multiple tipe
$accounts = $user->getAccountsByType(['AS', 'IN']);

// Hanya akun aktif
$activeAccounts = $user->getActiveAccounts();

// Summary akun user
$summary = $user->getAccountsSummary();

// Relasi langsung
$allAccounts = $user->financialAccounts()->get();
```

## Contoh Penggunaan di Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function assetReport()
    {
        $assets = FinancialAccount::ofType('AS')->active()->get();
        return view('reports.assets', ['assets' => $assets]);
    }

    public function incomeExpenseReport()
    {
        $incomeExpense = FinancialAccount::ofType(['IN', 'EX'])->get();
        return view('reports.income-expense', ['data' => $incomeExpense]);
    }

    public function userAccountsSummary(Request $request)
    {
        $user = $request->user();
        $summary = $user->getAccountsSummary();
        return response()->json($summary);
    }

    public function filterByType(Request $request)
    {
        $type = $request->input('type');
        
        if ($request->user()) {
            $accounts = $request->user()->getAccountsByType($type);
        } else {
            $accounts = FinancialAccount::ofType($type)->active()->get();
        }

        return response()->json($accounts);
    }
}
```

## Scope Methods Tersedia

### FinancialAccount

| Scope | Deskripsi | Contoh |
|-------|-----------|--------|
| `ofType($types)` | Filter berdasarkan tipe | `ofType('AS')` atau `ofType(['AS', 'IN'])` |
| `active($bool)` | Filter berdasarkan status aktif (default: true) | `active()` atau `active(false)` |
| `groups($bool)` | Filter grup akun (default: true) | `groups()` atau `groups(false)` |
| `activeByType($types)` | Kombinasi aktif + tipe | `activeByType('AS')` |

### Static Methods

| Method | Deskripsi | Return |
|--------|-----------|--------|
| `groupedByType()` | Group akun per tipe dengan count | Collection |
| `summaryByType()` | Summary per tipe (count + total_balance) | Collection |

### User

| Method | Deskripsi | Return |
|--------|-----------|--------|
| `getAccountsByType($types)` | Akun user berdasarkan tipe | Collection |
| `getActiveAccounts()` | Akun user yang aktif | Collection |
| `getAccountsSummary()` | Summary akun user | Collection |

## Notes

- Semua query otomatis memfilter akun yang `is_active = true` kecuali dinyatakan lain
- Multiple tipe dapat dipisahkan dengan koma atau array
- Semua response API include `count` untuk kemudahan
- Summary menampilkan `total_balance` untuk analisis keuangan

