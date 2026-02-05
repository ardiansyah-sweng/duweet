<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserFinancialAccount extends Model
{
    use HasFactory;

    protected $table = 'user_financial_accounts';

    protected $fillable = [
        'user_id',
        'user_account_id',
        'financial_account_id',
        'balance',
        'initial_balance',
        'is_active',
    ];

    public $timestamps = true;

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke FinancialAccount
     */
    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    /**
     * Sum balances of all users grouped by financial account type.
     * Only include financial accounts that are leaf (is_group = false) and active.
     * Returns associative array keyed by type (IN, EX, SP, LI, AS) with integer totals.
     *
     * @return array<string,int>
     */
    public static function sumAllUsersFinancialAccountsByType(): array
    {
        $types = ['IN', 'EX', 'SP', 'LI', 'AS'];

        $sql = <<<'SQL'
            SELECT fa.type, SUM(ufa.balance) AS total_balance
            FROM user_financial_accounts ufa
            JOIN financial_accounts fa ON ufa.financial_account_id = fa.id
            WHERE fa.is_group = ?
              AND fa.is_active = ?
              AND ufa.is_active = ?
            GROUP BY fa.type
        SQL;

        $rows = DB::select($sql, [0, 1, 1]);

        $result = array_fill_keys($types, 0);
        foreach ($rows as $r) {
            $result[$r->type] = (int) $r->total_balance;
        }

        return $result;
    }

    /**
     * Query DML untuk mendapatkan liquid assets semua user
     * 
     * @return array
     */
    public static function getAllUsersLiquidAssetsQuery($user_account_id = null)
    {
        $query = "
            SELECT 
                ufa.user_account_id,
                SUM(ufa.balance) as total_liquid_assets
            FROM 
                user_financial_accounts ufa
            INNER JOIN 
                financial_accounts fa ON ufa.financial_account_id = fa.id
            WHERE 
                fa.is_liquid = 1 
                AND fa.is_active = 1
                AND ufa.is_active = 1
                AND fa.type = 'AS'
        ";
        $bindings = [];
        if ($user_account_id !== null) {
            $query .= " AND ufa.user_account_id = ? ";
            $bindings[] = $user_account_id;
        }
        $query .= " GROUP BY ufa.user_account_id ORDER BY total_liquid_assets DESC ";
        return DB::select($query, $bindings);
    }

    /**
     * Ringkasan liquid asset untuk admin.
     * - total_all: total seluruh liquid asset aktif
     * - by_user: total per user_account_id
     * - by_account: total per akun keuangan (leaf, liquid)
     */
    public static function getAdminLiquidAssetsSummary(): array
    {
        $baseWhere = "fa.is_liquid = 1 AND fa.is_active = 1 AND ufa.is_active = 1 AND fa.type = 'AS'";

        $totalAll = DB::selectOne(
            "SELECT COALESCE(SUM(ufa.balance), 0) AS total_all FROM user_financial_accounts ufa
             INNER JOIN financial_accounts fa ON ufa.financial_account_id = fa.id
             WHERE {$baseWhere}"
        );

        $byUser = DB::select(
            "SELECT ufa.user_account_id, SUM(ufa.balance) AS total_liquid_assets
             FROM user_financial_accounts ufa
             INNER JOIN financial_accounts fa ON ufa.financial_account_id = fa.id
             WHERE {$baseWhere}
             GROUP BY ufa.user_account_id
             ORDER BY total_liquid_assets DESC"
        );

        $byAccount = DB::select(
            "SELECT fa.id AS financial_account_id, fa.name, fa.type, SUM(ufa.balance) AS total_liquid_assets
             FROM user_financial_accounts ufa
             INNER JOIN financial_accounts fa ON ufa.financial_account_id = fa.id
             WHERE {$baseWhere}
             GROUP BY fa.id, fa.name, fa.type
             ORDER BY total_liquid_assets DESC"
        );

        return [
            'total_all' => (int) ($totalAll->total_all ?? 0),
            'by_user' => array_map(function ($row) {
                return [
                    'user_account_id' => (int) $row->user_account_id,
                    'total_liquid_assets' => (int) $row->total_liquid_assets,
                    'formatted' => 'Rp ' . number_format($row->total_liquid_assets, 0, ',', '.'),
                ];
            }, $byUser),
            'by_account' => array_map(function ($row) {
                return [
                    'financial_account_id' => (int) $row->financial_account_id,
                    'name' => $row->name,
                    'type' => $row->type,
                    'total_liquid_assets' => (int) $row->total_liquid_assets,
                    'formatted' => 'Rp ' . number_format($row->total_liquid_assets, 0, ',', '.'),
                ];
            }, $byAccount),
        ];
    }

    /**
     * Query group balance user berdasarkan account type
     * 
     * @param string|null $accountType Filter berdasarkan tipe akun (IN, EX, SP, LI, AS)
     * @return array
     */
    public static function getGroupBalanceByAccountType($accountType = null)
    {
          $bindings = [];
        $accountTypeFilter = '';
        if ($accountType !== null) {
            $accountTypeFilter = " AND fa.type = ?";
            $bindings[] = strtoupper($accountType);
        }

        $sql = "
            SELECT
                ufa.user_account_id,
                fa.type AS account_type,
                SUM(ufa.initial_balance) AS total_initial_balance,
                SUM(ufa.balance) AS total_balance
            FROM
                user_financial_accounts ufa
            INNER JOIN
                financial_accounts fa ON ufa.financial_account_id = fa.id
            WHERE
                fa.is_active = 1
                AND ufa.is_active = 1
                {$accountTypeFilter}
            GROUP BY
                ufa.user_account_id, fa.type
            ORDER BY
                fa.type, ufa.user_account_id
        ";
     
        return DB::select($sql, $bindings);
    }
}
    