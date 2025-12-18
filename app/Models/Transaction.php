<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Constants\TransactionColumns;
use Carbon\Carbon; // Import Carbon untuk type hinting

class Transaction extends Model
{
    use HasFactory;

    // Nama tabel yang sesuai dengan konfigurasi
    protected $table = 'transactions';

    /**
     * Ambil ringkasan total pendapatan berdasarkan periode (Bulan) untuk user tertentu.
     * Ini adalah implementasi dari query: "sum income user by periode" dengan DML SQL murni.
     *
     * DML SQL (MySQL/MariaDB):
     * -----------------------------------------------------------
     * SELECT 
     *     DATE_FORMAT(t.created_at, '%Y-%m') AS periode,
     *     COALESCE(SUM(t.amount), 0) AS total_income
     * FROM transactions t
     * INNER JOIN financial_accounts fa ON t.financial_account_id = fa.id
     * WHERE t.user_account_id = ?
     *   AND fa.type = 'IN'
     *   AND t.balance_effect = 'increase'
     *   AND fa.is_group = 0
     *   AND t.created_at BETWEEN ? AND ?
     * GROUP BY DATE_FORMAT(t.created_at, '%Y-%m')
     * ORDER BY periode ASC;
     * -----------------------------------------------------------
     *
     * @param int $userAccountId
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function getIncomeSummaryByPeriod(int $userAccountId, Carbon $startDate, Carbon $endDate): \Illuminate\Support\Collection
    {
        // Gunakan nama tabel dari config bila ada, default ke nama tabel standar
        $transactionsTable = config('db_tables.transaction', 'transactions');
        $accountsTable = config('db_tables.financial_account', 'financial_accounts');

        // Tentukan fungsi format tanggal berdasarkan driver database
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
            $periodeExpr = "DATE_FORMAT(t.created_at, '%Y-%m')"; // MySQL/MariaDB
        }

        // Susun DML SQL murni (alias tabel: t, fa)
        $sql = "
            SELECT 
                {$periodeExpr} AS periode,
                COALESCE(SUM(t.amount), 0) AS total_income
            FROM {$transactionsTable} t
            INNER JOIN {$accountsTable} fa ON t.financial_account_id = fa.id
            WHERE 
                t.user_account_id = ?
                AND fa.type = 'IN'
                AND t.balance_effect = 'increase'
                AND fa.is_group = 0
                AND t.created_at BETWEEN ? AND ?
            GROUP BY {$periodeExpr}
            ORDER BY periode ASC
        ";

        // Eksekusi raw SQL dengan parameter binding
        $rows = DB::select($sql, [
            $userAccountId,
            $startDate->toDateTimeString(),
            $endDate->toDateTimeString(),
        ]);

        return collect($rows);
    }

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

    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class, TransactionColumns::USER_ACCOUNT_ID);
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, TransactionColumns::FINANCIAL_ACCOUNT_ID);
    }

    /**
     * Ambil detail transaksi lengkap via JOIN
     */
    public static function getDetailById($id)
    {
        return self::query()
        ->from('transactions as t')
        ->join('user_accounts as ua', 'ua.id', '=', 't.user_account_id')
        ->join('users as u', 'u.id', '=', 'ua.id_user')
        ->join('financial_accounts as fa', 'fa.id', '=', 't.financial_account_id')
        ->select(
            't.id as transaction_id',
            't.transaction_group_id',
            't.amount',
            't.entry_type',
            't.balance_effect',
            't.is_balance',
            't.description',
            't.created_at as transaction_date',
            'ua.id as user_account_id',
            'ua.username as user_account_username',
            'ua.email as user_account_email',
            'u.id as id_user',
            'u.name as user_name',
            'fa.id as financial_account_id',
            'fa.name as financial_account_name'
        )
        ->where('t.id', $id)
        ->first();
    }
}