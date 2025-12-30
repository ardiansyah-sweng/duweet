# Quick Copy-Paste: Postman Raw JSON

## 🚀 Cara Cepat Test di Postman

**URL:** `http://127.0.0.1:8000/api/transactions/1`  
**Method:** PUT  
**Headers:** `Content-Type: application/json`  
**Body:** raw → JSON

---

## ✅ Copy-Paste Langsung (Pilih Salah Satu)

### 1. Update Semua Field
```json
{
  "amount": 7500000,
  "description": "Gaji Bulanan Desember 2025 + Bonus Tahunan",
  "created_at": "2025-12-25 09:00:00"
}
```

### 2. Update Hanya Amount
```json
{
  "amount": 3500000
}
```

### 3. Update Hanya Description
```json
{
  "description": "Pembelian Laptop ASUS ROG - Updated"
}
```

### 4. Update Amount + Description
```json
{
  "amount": 2500000,
  "description": "Belanja Bulanan di Supermarket"
}
```

### 5. Update Description + Date
```json
{
  "description": "Bayar Listrik + Air - Desember 2025",
  "created_at": "2025-12-01 10:30:00"
}
```

### 6. Update Amount + Date
```json
{
  "amount": 4200000,
  "created_at": "2025-12-20 14:45:00"
}
```

---

## 💰 Contoh Transaksi Real

### Gaji
```json
{
  "amount": 8500000,
  "description": "Gaji Bulan Desember + Tunjangan",
  "created_at": "2025-12-01 08:00:00"
}
```

### Belanja
```json
{
  "amount": 1850000,
  "description": "Belanja Bulanan: Beras, Sayur, Buah, Lauk Pauk",
  "created_at": "2025-12-15 17:30:00"
}
```

### Tagihan
```json
{
  "amount": 650000,
  "description": "Bayar Listrik + Air + Internet",
  "created_at": "2025-12-05 10:00:00"
}
```

### Transportasi
```json
{
  "amount": 350000,
  "description": "Bensin Pertamax 25L",
  "created_at": "2025-12-20 08:15:00"
}
```

### Entertainment
```json
{
  "amount": 550000,
  "description": "Nonton Bioskop + Makan di Restaurant",
  "created_at": "2025-12-24 19:00:00"
}
```

---

## Format Tanggal yang Benar
```
Format: Y-m-d H:i:s
Contoh: 2025-12-30 15:45:30

✅ BENAR: "2025-12-25 09:00:00"
✅ BENAR: "2025-01-15 23:59:59"

❌ SALAH: "2025/12/25 09:00:00" (pakai slash)
❌ SALAH: "25-12-2025" (format Indonesia)
❌ SALAH: "2025-12-25" (tanpa jam)
```

---

## Test Error (untuk validasi)

### Amount Negatif
```json
{
  "amount": -500000
}
```

### Format Date Salah
```json
{
  "created_at": "30-12-2025"
}
```

---

## 🎯 Langkah-Langkah di Postman

1. **Buka Postman**
2. **Create New Request**
3. **Set Method:** PUT
4. **Set URL:** `http://127.0.0.1:8000/api/transactions/1`
5. **Set Headers:**
   - Key: `Content-Type`
   - Value: `application/json`
6. **Go to Body Tab:**
   - Select: **raw**
   - Select format: **JSON**
7. **Copy-paste salah satu JSON di atas**
8. **Click Send**
9. **Lihat Response!**

---

**Selamat Testing! 🚀**
