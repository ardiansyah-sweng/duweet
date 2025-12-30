# Postman Raw JSON Examples - Update Transaction

## Setup Postman

1. **Method:** PUT
2. **URL:** `http://127.0.0.1:8000/api/transactions/{id}` (ganti `{id}` dengan ID transaksi)
3. **Headers:**
   - `Content-Type: application/json`
   - `Accept: application/json`
4. **Body:** Pilih **raw** dan format **JSON**

---

## ✅ Example 1: Update Semua Field (Amount + Description + Date)

Copy-paste JSON ini ke body Postman:

```json
{
  "amount": 7500000,
  "description": "Gaji Bulanan Desember 2025 + Bonus Tahunan",
  "created_at": "2025-12-25 09:00:00"
}
```

**Use Case:** Update transaksi gaji yang sudah ada dengan nilai baru dan tanggal yang berbeda

---

## ✅ Example 2: Update Hanya Amount

```json
{
  "amount": 3500000
}
```

**Use Case:** Koreksi nilai transaksi tanpa merubah deskripsi atau tanggal

---

## ✅ Example 3: Update Hanya Description

```json
{
  "description": "Pembelian Laptop ASUS ROG - Edited"
}
```

**Use Case:** Memperjelas atau memperbaiki deskripsi transaksi

---

## ✅ Example 4: Update Amount + Description

```json
{
  "amount": 2500000,
  "description": "Belanja Bulanan di Supermarket Indomaret"
}
```

**Use Case:** Update nilai dan penjelasan transaksi sekaligus

---

## ✅ Example 5: Update Description + Date

```json
{
  "description": "Bayar Cicilan Motor - Bulan Desember",
  "created_at": "2025-12-01 10:30:00"
}
```

**Use Case:** Update penjelasan dan koreksi tanggal transaksi

---

## ✅ Example 6: Update Amount + Date

```json
{
  "amount": 4200000,
  "created_at": "2025-12-20 14:45:00"
}
```

**Use Case:** Koreksi nilai dan tanggal tanpa merubah deskripsi

---

## 📋 Skenario Real-World

### Skenario 1: Koreksi Gaji Salah Input
**Before:**
- Amount: 5,000,000
- Description: "Gaji Bulanan"

**Update dengan:**
```json
{
  "amount": 6500000,
  "description": "Gaji Bulanan + Tunjangan Hari Raya"
}
```

---

### Skenario 2: Update Pengeluaran dengan Detail Lebih Jelas
**Before:**
- Description: "Belanja"

**Update dengan:**
```json
{
  "description": "Belanja Bulanan: Beras 25kg, Minyak Goreng 5L, Gula 2kg, Telur 3kg"
}
```

---

### Skenario 3: Koreksi Tanggal Transaksi yang Salah
**Before:**
- Created_at: "2025-12-30 10:00:00"

**Update dengan:**
```json
{
  "created_at": "2025-12-28 15:30:00"
}
```

---

### Skenario 4: Update Transaksi Lengkap
**Before:**
- Amount: 800,000
- Description: "Bensin"
- Created_at: "2025-12-30 10:00:00"

**Update dengan:**
```json
{
  "amount": 850000,
  "description": "Bensin Pertamax 30L + Cuci Motor Premium",
  "created_at": "2025-12-29 16:20:00"
}
```

---

## 🧪 Test Cases untuk Validation Error

### Test: Amount Negatif (Harus Error 422)
```json
{
  "amount": -500000
}
```

**Expected Error:**
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

---

### Test: Amount Bukan Angka (Harus Error 422)
```json
{
  "amount": "lima juta"
}
```

**Expected Error:**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "amount": [
      "The amount field must be an integer."
    ]
  }
}
```

---

### Test: Format Tanggal Salah (Harus Error 422)
```json
{
  "created_at": "30-12-2025"
}
```

**Expected Error:**
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

---

### Test: Format Tanggal Salah - Gunakan Slash (Harus Error 422)
```json
{
  "created_at": "2025/12/30 10:00:00"
}
```

**Expected Error:**
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

---

### Test: Description Terlalu Panjang (Harus Error 422)
```json
{
  "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi aliquip"
}
```

**Expected Error:**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "description": [
      "The description field must not be greater than 1000 characters."
    ]
  }
}
```

---

## 💡 Tips Menggunakan Postman

### 1. Simpan Request ke Collection
- Klik tombol **Save** di Postman
- Buat collection baru bernama "Duweet API"
- Simpan request dengan nama yang deskriptif

### 2. Gunakan Environment Variables
Buat environment dengan variables:
```
base_url = http://127.0.0.1:8000
transaction_id = 1
```

Lalu gunakan di URL:
```
{{base_url}}/api/transactions/{{transaction_id}}
```

### 3. Gunakan Tests Tab
Tambahkan test script untuk verifikasi response:
```javascript
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

pm.test("Response has success field", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('success');
    pm.expect(jsonData.success).to.eql(true);
});

pm.test("Response has updated data", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('data');
});
```

---

## 📝 Checklist Testing

Gunakan checklist ini untuk memastikan semua test case sudah dicoba:

- [ ] ✅ Update semua field (amount + description + date)
- [ ] ✅ Update hanya amount
- [ ] ✅ Update hanya description
- [ ] ✅ Update hanya date
- [ ] ✅ Update amount + description
- [ ] ✅ Update amount + date
- [ ] ✅ Update description + date
- [ ] ❌ Test amount negatif (harus error)
- [ ] ❌ Test amount string (harus error)
- [ ] ❌ Test format date salah (harus error)
- [ ] ❌ Test description terlalu panjang (harus error)
- [ ] ❌ Test transaction ID tidak ada (harus error)

---

## 🎯 Quick Copy-Paste Examples

### Minimal Update
```json
{"amount": 1000000}
```

### Medium Update
```json
{"amount": 2500000, "description": "Updated transaction"}
```

### Full Update
```json
{"amount": 5000000, "description": "Complete update with new date", "created_at": "2025-12-28 10:00:00"}
```

### Indonesian Style
```json
{"amount": 7500000, "description": "Gaji Bulanan + THR + Bonus Kinerja Q4 2025", "created_at": "2025-12-31 08:00:00"}
```

---

## 🚀 Ready to Test!

1. Copy salah satu JSON example di atas
2. Buka Postman
3. Set method ke **PUT**
4. Set URL: `http://127.0.0.1:8000/api/transactions/1` (atau ID lainnya)
5. Set Headers: `Content-Type: application/json`
6. Paste JSON ke Body > raw > JSON
7. Click **Send**
8. Lihat response!

**Happy Testing! 🎉**
