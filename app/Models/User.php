<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi (mass assignable)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'usia',
        'bulan_lahir',
        'tanggal_lahir',
    ];

    /**
     * Kolom yang disembunyikan dari serialisasi
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Tipe data untuk casting otomatis
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke tabel user_financial_accounts
     */
    public function userFinancialAccounts()
    {
        return $this->hasMany(\App\Models\UserFinancialAccount::class, 'user_id');
    }

    /**
     * Query murni: ambil user yang tidak punya akun finansial sama sekali
     */
    public function scopeWithoutAccounts($query)
    {
        return $query->whereRaw("
            id NOT IN (
                SELECT user_id 
                FROM user_financial_accounts
            )
        ");
    }

    /**
     * Query murni: ambil user yang tidak punya akun finansial aktif
     */
    public function scopeWithoutActiveAccounts($query)
    {
        return $query->whereRaw("
            id NOT IN (
                SELECT user_id 
                FROM user_financial_accounts 
                WHERE is_active = true
            )
        ");
    }
}
