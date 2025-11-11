<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserAccount;
use App\Constants\TransactionColumns;
use App\Constants\UserAccountColumns;

class User extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
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
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * One user can have many user accounts (credentials)
     */
    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class, 'id_user');
    }

    /**
     * Get total transactions per user - simplified version.
     *
     * Returns essential transaction summary per user:
     * - User information (id, name, email)
     * - transaction_count: Number of transactions (count)
     * - total_transactions: Total money from all transactions (sum of amounts)
     * - total_debit & total_credit: Total money per type
     * - net_balance: Net balance (debit - credit)
     *
     * Usage: \App\Models\User::getTotalTransactionsPerUser();
     * Optional parameter: $userId
     *
     * @param  int|null  $userId  Filter by specific user ID
     * @return \Illuminate\Support\Collection
     */
    public static function getTotalTransactionsPerUser(?int $userId = null)
    {
        // Build a subquery that aggregates transactions grouped by the owning user (via user_accounts)
        $txSub = DB::table('transactions as t')
            ->join('user_accounts as ua', 't.' . TransactionColumns::USER_ACCOUNT_ID, '=', 'ua.' . UserAccountColumns::ID)
            ->selectRaw('ua.' . UserAccountColumns::ID_USER . ' as user_id')
            ->selectRaw('COUNT(t.id) as transaction_count')
            ->selectRaw('SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = ? THEN 1 ELSE 0 END) as debit_count', ['debit'])
            ->selectRaw('SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = ? THEN 1 ELSE 0 END) as credit_count', ['credit'])
            ->selectRaw('COALESCE(SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = ? THEN t.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as total_debit', ['debit'])
            ->selectRaw('COALESCE(SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = ? THEN t.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as total_credit', ['credit'])
            ->selectRaw('COALESCE(SUM(t.' . TransactionColumns::AMOUNT . '), 0) as total_transactions')
            ->groupBy('ua.' . UserAccountColumns::ID_USER);

        // Wrap subquery as a derived table and join with users
        $query = DB::table('users as u')
            ->leftJoinSub($txSub, 'tx', 'tx.user_id', '=', 'u.id')
            ->select([
                'u.id as user_id',
                'u.name as user_name',
                'u.email as user_email',
                DB::raw('COALESCE(tx.transaction_count, 0) as transaction_count'),
                DB::raw('COALESCE(tx.debit_count, 0) as debit_count'),
                DB::raw('COALESCE(tx.credit_count, 0) as credit_count'),
                DB::raw('COALESCE(tx.total_debit, 0) as total_debit'),
                DB::raw('COALESCE(tx.total_credit, 0) as total_credit'),
                DB::raw('COALESCE(tx.total_transactions, 0) as total_transactions'),
                DB::raw('COALESCE(tx.total_debit, 0) - COALESCE(tx.total_credit, 0) as net_balance'),
            ])
            ->orderByDesc('total_transactions');

        if ($userId !== null) {
            $query->where('u.id', $userId);
        }

        return $query->get();
    }
}
