<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // agar bisa digunakan untuk login
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAccount extends Authenticatable
{
    use HasFactory, Notifiable;

    // Nama tabel (opsional jika tabel = "user_accounts")
    protected $table = 'user_accounts';

    // Kolom yang bisa diisi secara mass-assignment
    protected $fillable = [
        'user_id',
        'username',
        'email',
        'password',
        'email_verified_at',
        'is_active',
    ];

    // Hidden field saat diubah jadi JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Tipe data otomatis dikonversi
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke model User
     * UserAccount dimiliki oleh satu User
     */
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function transactions()
    // {
    //     return $this->hasMany(Transaction::class, 'account_id');
    // }
}
