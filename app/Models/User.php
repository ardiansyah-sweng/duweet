<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'tanggal_lahir',
        'bulan_lahir',
        'tahun_lahir',
        'usia',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'integer',
            'bulan_lahir' => 'integer',
            'tahun_lahir' => 'integer',
            'usia' => 'integer',
        ];
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
        $selectFields = [
            // User Information
            'u.id as user_id',
            'u.name as user_name',
            'u.email as user_email',
            
            // Transaction Counts
            DB::raw('COUNT(DISTINCT t.id) as transaction_count'),
            DB::raw('SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = "debit" THEN 1 ELSE 0 END) as debit_count'),
            DB::raw('SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = "credit" THEN 1 ELSE 0 END) as credit_count'),
            
            // Transaction Amounts (Total Money)
            DB::raw('COALESCE(SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = "debit" THEN t.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as total_debit'),
            DB::raw('COALESCE(SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = "credit" THEN t.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as total_credit'),
            DB::raw('COALESCE(SUM(t.' . TransactionColumns::AMOUNT . '), 0) as total_transactions'),
            
            // Net Balance
            DB::raw('COALESCE(SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = "debit" THEN t.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN t.' . TransactionColumns::ENTRY_TYPE . ' = "credit" THEN t.' . TransactionColumns::AMOUNT . ' ELSE 0 END), 0) as net_balance'),
        ];
        
        $query = DB::table('users as u')
            ->leftJoin('user_accounts as ua', 'ua.' . UserAccountColumns::ID_USER, '=', 'u.id')
            ->leftJoin('transactions as t', 't.' . TransactionColumns::USER_ACCOUNT_ID, '=', 'ua.id')
            ->select($selectFields)
            ->groupBy('u.id', 'u.name', 'u.email')
            ->orderByDesc('total_transactions');

        // Filter by user ID if provided
        if ($userId !== null) {
            $query->where('u.id', $userId);
        }

        return $query->get();
    }
}
