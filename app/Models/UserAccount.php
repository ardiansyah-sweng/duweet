<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    use HasFactory;

    /**
     * This table does not use created_at/updated_at timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $table = 'user_accounts';

    protected $fillable = [
        'id_user',
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
        'verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke model User
     * UserAccount dimiliki oleh satu User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
        return $this->belongsTo(User::class, 'id_user');
    }

    // Contoh tambahan relasi jika nanti ada transaksi
    // public function transactions()
    // {
    //     return $this->hasMany(Transaction::class, 'account_id');
    // }
}
