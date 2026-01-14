<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\FinancialAccount;

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
     * Financial accounts linked to the user.
     * Assumption: pivot table `user_financial_accounts` with columns `user_id` and `financial_account_id`.
     */
    public function financialAccounts(): BelongsToMany
    {
        return $this->belongsToMany(FinancialAccount::class, 'user_financial_accounts', 'user_id', 'financial_account_id')
            ->withPivot(['balance', 'initial_balance', 'is_active']);
    }

    /**
     * Get user's financial accounts filtered by type
     */
    public function getAccountsByType($types)
    {
        return $this->financialAccounts()->whereIn('type', (array)$types)->get();
    }

    /**
     * Get user's active financial accounts
     */
    public function getActiveAccounts()
    {
        return $this->financialAccounts()->where('is_active', true)->get();
    }

    /**
     * Get user's account summary
     */
    public function getAccountsSummary()
    {
        return $this->financialAccounts()
            ->selectRaw('type, COUNT(*) as count, SUM(pivot_balance) as total_balance')
            ->groupBy('type')
            ->get();
    }
}
