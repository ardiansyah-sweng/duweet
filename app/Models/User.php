<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserAccount;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang bisa diisi (mass assignable)
     */
    /**
     * Disable automatic timestamps because users table does not have created_at/updated_at
     *
     * @var bool
     */
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

    /**
     * Kolom yang disembunyikan dari serialisasi (misal saat diubah ke JSON)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting atribut ke tipe data tertentu
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi ke UserAccount
    public function accounts()
    {
        return $this->hasMany(UserAccount::class, 'id_user');
    }

    // Relasi ke Transaction
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Relasi ke UserFinancialAccount
    public function userFinancialAccounts()
    {
        return $this->hasMany(UserFinancialAccount::class);
    }

    /**
     * Opsional: method bantu untuk mendapatkan nama lengkap secara dinamis
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}