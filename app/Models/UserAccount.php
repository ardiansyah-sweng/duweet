<?php

namespace App\Models;

use App\Constants\UserAccountColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAccount extends Model
{
    use HasFactory;

    protected $table = 'user_accounts';

    /**
     * Fillable columns
     */
    protected $fillable = [
        'user_id',
        'username',
        'email',
        'password',
        'verified_at',
        'is_active',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Relasi ke user
     * Satu akun login dimiliki satu user profile
     */
    public function user()
    {
        return $this->belongsTo(User::class, UserAccountColumns::ID_USER);
    }
}
