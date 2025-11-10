<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * ===============================
     * 🔗 Relasi antar tabel
     * ===============================
     */

    // Relasi ke tabel user_accounts
    public function accounts()
    {
        return $this->hasMany(UserAccount::class, 'id_user');
    }

    // Relasi ke tabel user_financial_accounts
    public function financialAccounts()
    {
        return $this->hasMany(UserFinancialAccount::class, 'user_id');
    }

    // Relasi ke tabel user_telephones
    public function telephones()
    {
        return $this->hasMany(UserTelephone::class, 'user_id');
    }
}