<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Constants\TransactionColumns;
use App\Constants\UserAccountColumns;
use App\Constants\UserFinancialAccountColumns;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;
    
    protected $table = 'transactions';

    protected $fillable = [];

    protected $casts = [
        TransactionColumns::AMOUNT => 'integer',
        TransactionColumns::IS_BALANCE => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = TransactionColumns::getFillable();
    }

    protected static function booted()
    {
        static::creating(function ($transaction) {
            if (empty($transaction->{TransactionColumns::TRANSACTION_GROUP_ID})) {
                $transaction->{TransactionColumns::TRANSACTION_GROUP_ID} = (string) Str::uuid();
            }
        });
    }

    // --- RELATIONSHIPS ---

    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class, TransactionColumns::USER_ACCOUNT_ID);
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, TransactionColumns::FINANCIAL_ACCOUNT_ID);
    }

    // --- QUERY METHODS ---

    public static function getIncomeSummaryByPeriod(int $userAccountId, Carbon $startDate, Carbon $endDate): \Illuminate\Support\Collection
    {
        $transactionsTable = config('db_tables.transaction', 'transactions');
        $accountsTable = config('db_tables.financial_account', 'financial_accounts');

        try {
            $driver = DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        } catch (\Exception $e) {
            $driver = 'mysql';
        }

        if ($driver === 'sqlite') {
            $periodeExpr = "strftime('%Y-%m', t.created_at)";
        } elseif ($driver === 'pgsql' || $driver === 'postgres') {
            $periodeExpr = "to_char(t.created_at, 'YYYY-MM')";
        } else {
            $periodeExpr = "DATE_FORMAT(t.created_at, '%Y-%m')";
        }

        $sql = "
            SELECT 
                {$periodeExpr} AS periode,
                COALESCE(SUM(t.amount), 0) AS total_income
            FROM {$transactionsTable} t
            INNER JOIN {$accountsTable} fa ON t.financial_account_id = fa.id
            WHERE t.user_account_id = ?
                AND fa.type = 'IN'
                AND t.balance_effect = 'increase'
                AND fa.is_group = 0
                AND t.created_at BETWEEN ? AND ?
            GROUP BY {$periodeExpr}
            ORDER BY periode ASC
        ";

        $rows = DB::select($sql, [
            $userAccountId,
            $startDate->toDateTimeString(),
            $endDate->toDateTimeString(),
        ]);

        return collect($rows);
    }

    public static function getTotalTransactionsPerUserAccount(?int $userAccountId = null)
    {
        $transactionTable = config('db_tables.transaction', 'transactions');
        $userAccountTable = config('db_tables.user_account', 'user_accounts');
        $transactionGroupIdCol = TransactionColumns::TRANSACTION_GROUP_ID;
        $userAccountIdCol = TransactionColumns::USER_ACCOUNT_ID;

        $whereClause = $userAccountId !== null ? "WHERE user_accounts.id = ?" : "";
        $bindings = $userAccountId !== null ? [$userAccountId] : [];

        $sql = "
            SELECT 
                user_accounts.id AS user_account_id,
                user_accounts.email AS user_account_email,
                COUNT(DISTINCT transactions.{$transactionGroupIdCol}) AS transaction_count
            FROM {$userAccountTable} AS user_accounts
            LEFT JOIN {$transactionTable} AS transactions ON user_accounts.id = transactions.{$userAccountIdCol}
            {$whereClause}
            GROUP BY user_accounts.id, user_accounts.email
            ORDER BY transaction_count DESC, user_accounts.id ASC
        ";

        return collect(DB::select($sql, $bindings));
    }

    public static function getSurplusDefisitByPeriod(int $userAccountId, Carbon $startDate, Carbon $endDate): array 
    {
        $transactionsTable = config('db_tables.transaction', 'transactions');
        $financialAccountsTable = config('db_tables.financial_account', 'financial_accounts');

        $sql = "
            SELECT
                SUM(CASE WHEN fa.type = 'IN' THEN t.amount ELSE 0 END) AS total_income,
                SUM(CASE WHEN fa.type = 'EX' THEN t.amount ELSE 0 END) AS total_expense,
                (SUM(CASE WHEN fa.type = 'IN' THEN t.amount ELSE 0 END) - SUM(CASE WHEN fa.type = 'EX' THEN t.amount ELSE 0 END)) AS surplus_defisit
            FROM {$transactionsTable} t
            JOIN {$financialAccountsTable} fa ON fa.id = t.financial_account_id
            WHERE t.user_account_id = ? AND t.created_at BETWEEN ? AND ?
        ";

        return (array) DB::selectOne($sql, [$userAccountId, $startDate->toDateTimeString(), $endDate->toDateTimeString()]);
    }

    // --- DELETE METHODS ---

    public static function deleteTransactionById(int $transactionId): bool
    {
        $transaction = static::find($transactionId);
        return $transaction ? $transaction->delete() : false;
    }

    public static function deleteByUserAccountIds($userAccountIds): int
    {
        if (empty($userAccountIds) || count($userAccountIds) === 0) return 0;
        return DB::table((new self)->getTable())->whereIn('user_account_id', $userAccountIds)->delete();
    }

    public static function hardDeleteByGroupId(string $groupId): array
    {
        try {
            $deletedCount = 0;
            DB::transaction(function () use ($groupId, &$deletedCount) {
                $transactions = DB::table((new self)->getTable())->where(TransactionColumns::TRANSACTION_GROUP_ID, $groupId)->get();
                if ($transactions->isEmpty()) throw new \Exception('Group not found');

                foreach ($transactions as $t) {
                    // Update saldo logic here if needed...
                    DB::table((new self)->getTable())->where('id', $t->id)->delete();
                    $deletedCount++;
                }
            });
            return ['success' => true, 'message' => 'Deleted', 'deleted_count' => $deletedCount];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'deleted_count' => 0];
        }
    }
}