<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // agar bisa digunakan untuk login
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAccount extends Authenticatable
{
    use HasFactory, Notifiable;

    // Nama tabel sesuai config
    protected $table = 'user_accounts';

    // Primary key
    protected $primaryKey = 'id';

    // Nonaktifkan timestamps karena migration tidak membuat created_at & updated_at
    public $timestamps = false;

    // Kolom yang bisa diisi secara mass-assignment
    protected $fillable = [
        'user_id',
        'username',
        'email',
        'password',
        'verified_at',
        'is_active',
    ];

    // Hidden field saat diubah jadi JSON
    protected $hidden = [
        'password',
    ];

    // Casting tipe data
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke model User
     * UserAccount dimiliki oleh satu User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Contoh tambahan relasi jika nanti ada transaksi
    // public function transactions()
    // {
    //     return $this->hasMany(Transaction::class, 'account_id');
    // }
}
