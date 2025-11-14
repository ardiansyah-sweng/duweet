<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'usia',
        'bulan_lahir',
        'tanggal_lahir',
    ];

   
    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function userFinancialAccounts()
    {
        return $this->hasMany(\App\Models\UserFinancialAccount::class, 'user_id');
    }

    public function scopeWithoutAccounts($query)
    {
        return $query->whereRaw("
            id NOT IN (
                SELECT user_id 
                FROM user_financial_accounts
            )
        ");
    }

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
