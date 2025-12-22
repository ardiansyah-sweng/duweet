<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns;

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
     * Get user's financial accounts (if `user_id` exists on financial_accounts).
     */
    public function financialAccounts()
    {
        return $this->hasMany(FinancialAccount::class, 'user_id');
    }

    /**
     * Get total balance for the user by summing account balances.
     * Returns int (0 if no accounts or column missing).
     */
    public function totalBalance(): int
    {
        try {
            return (int) $this->financialAccounts()->sum(FinancialAccountColumns::BALANCE);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
