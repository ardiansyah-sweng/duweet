<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

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
     * Get the transactions for this user.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Static helper: get total transactions per user with flexible filtering
     * 
     * @param array|null $userIds - filter by specific user IDs
     * @param string|null $groupBy - 'account-type' to group by account type, null for detailed breakdown
     * @return Collection
     */
    public static function getTotalTransactionStats(?array $userIds = null, ?string $groupBy = null): Collection
    {
        if ($groupBy === 'account-type') {
            // Group by account type
            $query = DB::table('users')
                ->leftJoin('transactions', 'users.id', '=', 'transactions.user_id')
                ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'accounts.type',
                    DB::raw('COALESCE(SUM(transactions.amount), 0) as total_amount'),
                    DB::raw('COALESCE(COUNT(transactions.id), 0) as transaction_count'),
                    DB::raw('COALESCE(SUM(CASE WHEN transactions.balance_effect = "increase" THEN transactions.amount WHEN transactions.balance_effect = "decrease" THEN -transactions.amount ELSE 0 END), 0) as net_balance_effect'),
                    DB::raw('COALESCE(COUNT(DISTINCT transactions.transaction_group_id), 0) as transaction_group_count')
                )
                ->groupBy('users.id', 'users.name', 'users.email', 'accounts.type');
        } else {
            // Default: detailed breakdown (debit/credit)
            $query = DB::table('users')
                ->leftJoin('transactions', 'users.id', '=', 'transactions.user_id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    DB::raw('COALESCE(SUM(transactions.amount), 0) as total_transaction_amount'),
                    DB::raw('COALESCE(SUM(CASE WHEN transactions.entry_type = "debit" THEN transactions.amount ELSE 0 END), 0) as total_debit'),
                    DB::raw('COALESCE(SUM(CASE WHEN transactions.entry_type = "credit" THEN transactions.amount ELSE 0 END), 0) as total_credit'),
                    DB::raw('COALESCE(COUNT(transactions.id), 0) as transaction_count'),
                    DB::raw('COALESCE(COUNT(CASE WHEN transactions.entry_type = "debit" THEN 1 END), 0) as debit_count'),
                    DB::raw('COALESCE(COUNT(CASE WHEN transactions.entry_type = "credit" THEN 1 END), 0) as credit_count'),
                    DB::raw('COALESCE(SUM(CASE WHEN transactions.balance_effect = "increase" THEN transactions.amount WHEN transactions.balance_effect = "decrease" THEN -transactions.amount ELSE 0 END), 0) as net_balance_effect'),
                    DB::raw('COALESCE(COUNT(DISTINCT transactions.transaction_group_id), 0) as transaction_group_count')
                )
                ->groupBy('users.id', 'users.name', 'users.email');
        }

        if (!empty($userIds)) {
            $query->whereIn('users.id', $userIds);
        }

        return $query->get();
    }
}
