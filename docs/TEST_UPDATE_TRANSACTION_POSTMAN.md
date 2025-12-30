# Test Endpoint Update Transaction di Postman

## Prerequisites
✅ Database sudah di-seed
✅ Server Laravel running di http://127.0.0.1:8000
✅ Ada 606 transaksi di database

## Step 1: Cari ID Transaksi yang Akan Diupdate

### Request
```
GET http://127.0.0.1:8000/api/transactions
```

### Response Example
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_account_id": 1,
      "financial_account_id": 5,
      "transaction_group_id": "abc123...",
      "entry_type": "debit",
      "amount": 5000000,
      "balance_effect": "increase",
      "description": "Gaji Bulanan",
      "is_balance": 0,
      "created_at": "2025-12-30T09:20:00.000000Z",
      "updated_at": "2025-12-30T09:20:00.000000Z"
    }
    // ... more transactions
  ],
  "count": 606
}
```

Pilih salah satu ID transaction, misalnya **ID: 1**

---

## Step 2: Lihat Detail Transaksi Sebelum Update

### Request
```
GET http://127.0.0.1:8000/api/transactions/1
```

### Response
```json
{
  "success": true,
  "data": {
    "transaction_id": 1,
    "transaction_group_id": "abc123...",
    "amount": 5000000,
    "entry_type": "debit",
    "balance_effect": "increase",
    "is_balance": 0,
    "description": "Gaji Bulanan",
    "transaction_date": "2025-12-30T09:20:00.000000Z",
    "user_account_id": 1,
    "user_account_username": "john.doe",
    "user_account_email": "john@example.com",
    "id_user": 1,
    "user_name": "John Doe",
    "financial_account_id": 5,
    "financial_account_name": "Kas"
  }
}
```

**Catat:**
- Amount sebelum: **5,000,000**
- Description sebelum: **"Gaji Bulanan"**

---

## Step 3: Update Transaksi

### Request
```
PUT http://127.0.0.1:8000/api/transactions/1
Content-Type: application/json
```

### Body
```json
{
  "amount": 6500000,
  "description": "Gaji Bulanan + Bonus Performance",
  "created_at": "2025-12-29 08:00:00"
}
```

### Expected Response
```json
{
  "success": true,
  "message": "Transaction updated successfully",
  "data": {
    "id": 1,
    "user_account_id": 1,
    "financial_account_id": 5,
    "transaction_group_id": "abc123...",
    "entry_type": "debit",
    "amount": 6500000,
    "balance_effect": "increase",
    "description": "Gaji Bulanan + Bonus Performance",
    "is_balance": 0,
    "created_at": "2025-12-29T08:00:00.000000Z",
    "updated_at": "2025-12-30T09:25:00.000000Z"
  }
}
```

---

## Step 4: Verifikasi Update

### Request
```
GET http://127.0.0.1:8000/api/transactions/1
```

Pastikan data sudah terupdate:
- ✅ Amount berubah: 5,000,000 → **6,500,000**
- ✅ Description berubah: "Gaji Bulanan" → **"Gaji Bulanan + Bonus Performance"**
- ✅ Created_at berubah: sesuai request

---

## Test Cases Lainnya

### Test Case 1: Update Hanya Amount
```json
PUT http://127.0.0.1:8000/api/transactions/2
{
  "amount": 2000000
}
```

### Test Case 2: Update Hanya Description
```json
PUT http://127.0.0.1:8000/api/transactions/3
{
  "description": "Belanja Bulanan - Updated"
}
```

### Test Case 3: Invalid Amount (Harus Error)
```json
PUT http://127.0.0.1:8000/api/transactions/4
{
  "amount": -1000
}
```

**Expected Response:**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "amount": [
      "The amount field must be at least 0."
    ]
  }
}
```

### Test Case 4: Invalid Date Format (Harus Error)
```json
PUT http://127.0.0.1:8000/api/transactions/5
{
  "created_at": "2025/12/30"
}
```

**Expected Response:**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "created_at": [
      "The created at field must match the format Y-m-d H:i:s."
    ]
  }
}
```

### Test Case 5: Transaction Not Found (Harus Error)
```json
PUT http://127.0.0.1:8000/api/transactions/99999
{
  "amount": 1000000
}
```

**Expected Response:**
```json
{
  "success": false,
  "message": "Failed to update transaction: Transaction not found: 99999",
  "data": null
}
```

---

## Checklist Testing
- [ ] ✅ Get list transactions
- [ ] ✅ Get detail transaction sebelum update
- [ ] ✅ Update amount, description, dan created_at
- [ ] ✅ Verifikasi data sudah berubah
- [ ] ✅ Test update hanya 1 field
- [ ] ✅ Test validation error (amount negatif)
- [ ] ✅ Test validation error (format date salah)
- [ ] ✅ Test transaction not found

---

## Postman Collection JSON

Anda bisa import collection ini ke Postman:

```json
{
  "info": {
    "name": "Duweet Transaction Update API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Get All Transactions",
      "request": {
        "method": "GET",
        "header": [],
        "url": {
          "raw": "http://127.0.0.1:8000/api/transactions",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "transactions"]
        }
      }
    },
    {
      "name": "Get Transaction Detail",
      "request": {
        "method": "GET",
        "header": [],
        "url": {
          "raw": "http://127.0.0.1:8000/api/transactions/1",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "transactions", "1"]
        }
      }
    },
    {
      "name": "Update Transaction",
      "request": {
        "method": "PUT",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"amount\": 6500000,\n  \"description\": \"Gaji Bulanan + Bonus Performance\",\n  \"created_at\": \"2025-12-29 08:00:00\"\n}"
        },
        "url": {
          "raw": "http://127.0.0.1:8000/api/transactions/1",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "transactions", "1"]
        }
      }
    }
  ]
}
```

Save JSON di atas ke file `Duweet_Transaction_Update.postman_collection.json` dan import ke Postman!
