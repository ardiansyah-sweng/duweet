<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserAccount extends Model
{
    use HasFactory;

    protected $table = 'user_accounts';
    // ... properti lain seperti fillable/guarded biarkan saja ...

    /**
     * Update User Account menggunakan Raw SQL Query
     * Aman dari SQL Injection karena menggunakan binding (?)
     */
    public static function updateRaw($id, $data)
    {
        // 1. Siapkan kolom yang akan di-update secara dinamis
        $sets = [];
        $bindings = [];

        // Cek satu per satu key yang dikirim controller
        if (array_key_exists('username', $data)) {
            $sets[] = 'username = ?';
            $bindings[] = $data['username'];
        }

        if (array_key_exists('email', $data)) {
            $sets[] = 'email = ?';
            $bindings[] = $data['email'];
        }

        if (array_key_exists('is_active', $data)) {
            $sets[] = 'is_active = ?';
            $bindings[] = (int) $data['is_active']; // Konversi boolean true/false jadi 1/0
        }

        // Jika array $sets kosong, berarti tidak ada field valid yang diupdate
        if (empty($sets)) {
            return false;
        }

        // 2. Selalu update kolom updated_at (Manual karena Raw Query)
        $sets[] = 'updated_at = ?';
        $bindings[] = now(); // Helper Laravel untuk waktu sekarang

        // 3. Masukkan ID ke binding terakhir untuk WHERE clause
        $bindings[] = $id;

        // Gabungkan string query
        $setString = implode(', ', $sets);
        
        // Query final: "UPDATE user_accounts SET username = ?, updated_at = ? WHERE id = ?"
        $query = "UPDATE user_accounts SET {$setString} WHERE id = ?";

        // 4. Eksekusi
        // DB::update mengembalikan jumlah baris yang terpengaruh (int)
        $affectedRows = DB::update($query, $bindings);

        return $affectedRows > 0;
    }

    /**
     * Mengembalikan object standard (stdClass), bukan Model Eloquent
     */
    public static function findRaw($id)
    {
        // LIMIT 1 agar efisien
        $query = "SELECT * FROM user_accounts WHERE id = ? LIMIT 1";
        $result = DB::select($query, [$id]);

        // DB::select selalu mengembalikan array. Kita ambil index ke-0 jika ada.
        return !empty($result) ? $result[0] : null;
    }
}