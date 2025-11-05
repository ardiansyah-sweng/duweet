<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\UserColumns;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $primaryKey = UserColumns::ID;
    protected $fillable = UserColumns::getFillable();

    protected $casts = [
        UserColumns::TANGGAL_LAHIR => 'integer',
        UserColumns::BULAN_LAHIR   => 'integer',
        UserColumns::TAHUN_LAHIR   => 'integer',
        UserColumns::USIA          => 'integer',
    ];

    /**
     * Relasi ke tabel user_accounts
     * (1 user dapat memiliki banyak akun login)
     */
    public function accounts()
    {
        return $this->hasMany(UserAccount::class, 'user_id', 'id');
    }

    /**
     * 🔍 Scope: Query untuk menampilkan user yang masih aktif
     * digunakan oleh admin untuk melihat daftar user aktif.
     *
     * Kriteria:
     * - user memiliki akun aktif (is_active = true)
     * - bisa ditambah filter email_verified_at jika perlu
     */
    public function scopeActiveUsers($query)
    {
        return $query->whereHas('accounts', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * 📦 Static method: ambil daftar user aktif lengkap dengan akun aktif mereka.
     * Berguna untuk admin dashboard.
     */
    public static function getActiveUsers()
    {
        return self::with(['accounts' => function ($q) {
            $q->where('is_active', true)
              ->select('id', 'user_id', 'username', 'email', 'is_active');
        }])
        ->whereHas('accounts', function ($q) {
            $q->where('is_active', true);
        })
        ->orderBy(UserColumns::NAME)
        ->get([
            UserColumns::ID,
            UserColumns::NAME,
            UserColumns::EMAIL,
        ]);
    }
}