<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB; // <— WAJIB agar bisa pakai query SQL
use App\Models\UserAccount;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'jalan',
        'kode_pos',
        'tanggal_lahir',
        'bulan_lahir',
        'tahun_lahir',
        'usia',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'id_user');
    }

    /* ============================================================
     |  DML QUERY:  (SELECT, INSERT, UPDATE, DELETE)
     | ============================================================
     */

    // SELECT: Ambil semua user
    public static function getAllUsers()
    {
        return DB::select('SELECT * FROM users');
    }

    // SELECT: Ambil user berdasarkan ID
    public static function getUserById($id)
    {
        return DB::select('SELECT * FROM users WHERE id = ?', [$id]);
    }

    // INSERT: Tambah user baru
    public static function insertUser($data)
    {
        return DB::insert(
            'INSERT INTO users (name, email, password) VALUES (?, ?, ?)',
            [$data['name'], $data['email'], $data['password']]
        );
    }

    // UPDATE: Update email user
    public static function updateEmail($id, $email)
    {
        return DB::update(
            'UPDATE users SET email = ? WHERE id = ?',
            [$email, $id]
        );
    }

    // DELETE: Hapus user
    public static function deleteUser($id)
    {
        return DB::delete('DELETE FROM users WHERE id = ?', [$id]);
    }
}
