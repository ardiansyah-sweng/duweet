# Dokumentasi API Edit Transaksi

## Endpoint Update Transaksi

**URL:** `PUT http://localhost:8000/api/transactions/{id}`

**Method:** PUT

**Description:** Update transaksi existing (amount, description, created_at) dan otomatis menyesuaikan saldo di user_financial_accounts

---

## Request Parameters

### Path Parameter
- `id` (required) - ID transaksi yang akan diupdate

### Body (JSON - Optional Fields)
Semua field bersifat optional, hanya field yang dikirim yang akan diupdate:

```json
{
  "amount": 150000,
  "description": "Beli buku pemrograman Laravel - Updated",
  "created_at": "2025-12-25 10:30:00"
}
```

#### Field Details:
- **amount** (integer, optional) - Nominal transaksi baru (harus >= 0)
- **description** (string, optional) - Deskripsi transaksi baru (max 1000 karakter)
- **created_at** (string, optional) - Tanggal transaksi dalam format `Y-m-d H:i:s` (contoh: `2025-12-25 10:30:00`)

---

## Response Examples

### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Transaction updated successfully",
  "data": {
    "id": 5,
    "user_account_id": 1,
    "financial_account_id": 3,
    "transaction_group_id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
    "entry_type": "debit",
    "amount": 150000,
    "balance_effect": "increase",
    "description": "Beli buku pemrograman Laravel - Updated",
    "is_balance": 0,
    "created_at": "2025-12-25T10:30:00.000000Z",
    "updated_at": "2025-12-30T16:00:00.000000Z"
  }
}
```

### Validation Error (422 Unprocessable Entity)
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "amount": [
      "The amount field must be an integer.",
      "The amount field must be at least 0."
    ],
    "created_at": [
      "The created at field must match the format Y-m-d H:i:s."
    ]
  }
}
```

### Error Response (400 Bad Request)
```json
{
  "success": false,
  "message": "Failed to update transaction: Transaction not found: 999",
  "data": null
}
```

### Server Error (500 Internal Server Error)
```json
{
  "success": false,
  "message": "Gagal update transaksi: Error message here"
}
```

---

## Cara Test di Postman

### 1. Setup Request
1. Buka Postman
2. Buat request baru
3. Set method ke **PUT**
4. Set URL: `http://localhost:8000/api/transactions/{id}` (ganti `{id}` dengan ID transaksi yang ingin diupdate)

### 2. Set Headers
- `Content-Type: application/json`
- `Accept: application/json`

### 3. Set Body
1. Pilih tab **Body**
2. Pilih **raw**
3. Pilih format **JSON**
4. Masukkan data yang ingin diupdate:

```json
{
  "amount": 250000,
  "description": "Shopping di mall - Edited"
}
```

Atau untuk update semua field:

```json
{
  "amount": 350000,
  "description": "Bayar listrik bulan Desember",
  "created_at": "2025-12-28 15:45:00"
}
```

### 4. Send Request
Klik tombol **Send**

---

## Contoh Penggunaan

### Contoh 1: Update hanya Amount
**Request:**
```
PUT http://localhost:8000/api/transactions/5
```
```json
{
  "amount": 200000
}
```

### Contoh 2: Update Description dan Created At
**Request:**
```
PUT http://localhost:8000/api/transactions/10
```
```json
{
  "description": "Pembelian laptop gaming",
  "created_at": "2025-12-20 09:00:00"
}
```

### Contoh 3: Update semua field sekaligus
**Request:**
```
PUT http://localhost:8000/api/transactions/15
```
```json
{
  "amount": 500000,
  "description": "Gaji bulan Desember 2025",
  "created_at": "2025-12-01 08:00:00"
}
```

---

## Flow Proses Update

1. **Validasi Input** - Cek apakah data yang dikirim valid
2. **Lock Transaksi** - Ambil data transaksi lama dengan database lock (FOR UPDATE)
3. **Rollback Saldo Lama** - Kembalikan saldo ke kondisi sebelum transaksi dilakukan
4. **Update Transaksi** - Update field amount, description, dan/atau created_at
5. **Apply Saldo Baru** - Terapkan saldo baru berdasarkan amount yang baru
6. **Return Data** - Kembalikan data transaksi yang sudah diupdate

---

## Catatan Penting

⚠️ **PENTING:**
- Query ini menggunakan **raw SQL murni** sesuai dengan pattern yang ada di model
- Update transaksi akan **otomatis menyesuaikan saldo** di tabel `user_financial_accounts`
- Proses update dilakukan dalam **database transaction** untuk memastikan data consistency
- Field `entry_type`, `balance_effect`, `user_account_id`, dan `financial_account_id` **tidak bisa diubah**
- Hanya field `amount`, `description`, dan `created_at` yang bisa diupdate
- Jika tidak mengirim field tertentu, field tersebut tidak akan berubah (tetap seperti nilai lama)

---

## Testing Checklist

- [ ] Test update hanya amount
- [ ] Test update hanya description  
- [ ] Test update hanya created_at
- [ ] Test update kombinasi beberapa field
- [ ] Test dengan ID transaksi yang tidak ada (harus error 400)
- [ ] Test dengan amount negatif (harus error validasi)
- [ ] Test dengan format created_at yang salah (harus error validasi)
- [ ] Cek saldo di `user_financial_accounts` sebelum dan sesudah update
