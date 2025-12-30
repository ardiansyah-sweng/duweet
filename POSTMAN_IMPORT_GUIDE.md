# 🚀 Cara Import & Test di Postman

## 📦 Import Postman Collection

### Langkah 1: Import Collection File
1. Buka **Postman**
2. Klik tombol **Import** (di kiri atas)
3. Pilih tab **File**
4. Drag & drop file `Duweet_Transaction_Update.postman_collection.json`
   
   **ATAU**
   
   Klik **Choose Files** → Pilih file `Duweet_Transaction_Update.postman_collection.json`

5. Klik **Import**

### Langkah 2: Collection Berhasil Di-Import!
Anda akan melihat collection baru bernama **"Duweet - Transaction Update API"** dengan 11 request:

- ✅ 1. Get All Transactions
- ✅ 2. Get Transaction by ID
- ✅ 3. Update - All Fields
- ✅ 4. Update - Amount Only
- ✅ 5. Update - Description Only
- ✅ 6. Update - Amount + Description
- ✅ 7. Update - Real: Gaji
- ✅ 8. Update - Real: Belanja
- ✅ 9. Test Error - Negative Amount
- ✅ 10. Test Error - Invalid Date Format
- ✅ 11. Test Error - Transaction Not Found

---

## 🎯 Cara Test Setiap Request

### Test 1: Get All Transactions (Cek Data yang Ada)
1. Klik request **"1. Get All Transactions"**
2. Klik **Send**
3. Lihat list semua transaksi
4. **Catat ID transaksi** yang ingin diupdate

### Test 2: Get Transaction by ID (Lihat Data Sebelum Update)
1. Klik request **"2. Get Transaction by ID"**
2. Ubah ID di URL sesuai yang ingin dilihat (default: 1)
3. Klik **Send**
4. **Catat nilai amount, description, created_at** sebelum update

### Test 3: Update All Fields (Update Lengkap)
1. Klik request **"3. Update - All Fields"**
2. Ubah ID di URL sesuai transaksi yang ingin diupdate
3. Edit JSON body sesuai kebutuhan:
   ```json
   {
     "amount": 7500000,
     "description": "Gaji Bulanan Desember 2025 + Bonus Tahunan",
     "created_at": "2025-12-25 09:00:00"
   }
   ```
4. Klik **Send**
5. Cek response: `"success": true`

### Test 4-8: Update Partial Fields
- Pilih salah satu request (4-8)
- Ubah ID di URL
- Edit body JSON sesuai kebutuhan
- Klik **Send**

### Test 9-11: Test Error Cases
- Gunakan request ini untuk test validasi
- Harus mendapat response error (422 atau 400)
- Verifikasi error message sesuai validasi

---

## 📝 Cara Modifikasi Request

### Mengubah Transaction ID
Di setiap request, ubah angka di URL:
```
http://127.0.0.1:8000/api/transactions/1  ← Ubah angka 1 ini
```

### Mengubah Body JSON
Di tab **Body**, edit JSON sesuai kebutuhan:
```json
{
  "amount": 5000000,           ← Ubah nilai ini
  "description": "Text baru",  ← Ubah teks ini
  "created_at": "2025-12-25 09:00:00"  ← Ubah tanggal ini
}
```

---

## ✅ Checklist Testing

### Basic Tests
- [ ] Import collection ke Postman
- [ ] Get list semua transaksi
- [ ] Get detail 1 transaksi
- [ ] Update semua field (amount + description + date)
- [ ] Verifikasi data berhasil berubah

### Partial Update Tests
- [ ] Update hanya amount
- [ ] Update hanya description
- [ ] Update amount + description
- [ ] Update description + date

### Real-World Tests
- [ ] Test update transaksi gaji
- [ ] Test update transaksi belanja

### Error Validation Tests
- [ ] Test amount negatif (harus error 422)
- [ ] Test format tanggal salah (harus error 422)
- [ ] Test transaction ID tidak ada (harus error 400)

---

## 🔧 Troubleshooting

### Error: Connection Refused
**Masalah:** Server Laravel tidak running

**Solusi:**
```bash
cd d:\XAMPP\htdocs\duweet2\duweet
php artisan serve
```

### Error: Transaction Not Found
**Masalah:** ID transaksi tidak ada di database

**Solusi:** 
1. Get list transaksi dulu dengan request #1
2. Gunakan ID yang valid dari response

### Error: Validation Failed
**Masalah:** Format data tidak sesuai validasi

**Solusi:**
- Amount harus angka positif (>= 0)
- Date harus format: `Y-m-d H:i:s` (contoh: `2025-12-30 15:00:00`)
- Description maksimal 1000 karakter

---

## 💡 Tips & Tricks

### 1. Gunakan Environment Variables
Buat environment untuk development:
```
base_url = http://127.0.0.1:8000
```

Lalu ubah URL di collection jadi:
```
{{base_url}}/api/transactions/1
```

### 2. Save Response untuk Comparison
- Klik **Save Response** setelah Get Transaction
- Bandingkan dengan response setelah Update
- Verifikasi perubahan data

### 3. Duplicate Request untuk Custom Test
- Right-click pada request
- Pilih **Duplicate**
- Edit sesuai kebutuhan test Anda

### 4. Tambahkan Tests Script
Klik tab **Tests**, tambahkan script:
```javascript
pm.test("Update successful", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.success).to.eql(true);
});
```

---

## 📚 Dokumentasi Lengkap

- **Quick Guide:** Lihat file `POSTMAN_QUICK_GUIDE.md`
- **Raw JSON Examples:** Lihat file `docs/POSTMAN_RAW_JSON_EXAMPLES.md`
- **API Documentation:** Lihat file `docs/API_UPDATE_TRANSACTION.md`

---

## 🎉 Ready to Test!

**Collection sudah siap digunakan!** 

Mulai dari request **#1** untuk melihat data, lalu coba request **#3** untuk update pertama Anda!

**Happy Testing! 🚀**
