<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class UserFinancialAccount extends Model
{
    use HasFactory;

    protected $table = 'user_financial_accounts';

    protected $fillable = [
        'user_id',
        'user_account_id',
        'financial_account_id',
        'account_number',
        'balance',
        'initial_balance',
        'parent_account_id',
        'is_active',
    ];

    /* =========================
     | RELATIONS
     ========================= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userAccount(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id');
    }

    public function financialAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    /* =========================
     | NESTED ACCOUNT (TREE)
     ========================= */

    public function parentAccount(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_account_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_account_id');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    /* =========================
     | RAW QUERIES (DML)
     ========================= */

    public static function sumAllUsersFinancialAccountsByType(): array
    {
        $types = ['IN', 'EX', 'SP', 'LI', 'AS'];

        $sql = "
            SELECT fa.type, SUM(ufa.balance) AS total_balance
            FROM user_financial_accounts ufa
            INNER JOIN financial_accounts fa ON ufa.financial_account_id = fa.id
            WHERE fa.is_group = 0
              AND fa.is_active = 1
              AND ufa.is_active = 1
            GROUP BY fa.type
        ";

        $rows = DB::select($sql);

        $result = array_fill_keys($types, 0);
        foreach ($rows as $row) {
            $result[$row->type] = (int) $row->total_balance;
        }

        return $result;
    }

    public static function getAllUsersLiquidAssetsQuery($user_account_id = null)
    {
        $sql = "
            SELECT 
                ufa.user_account_id,
                SUM(ufa.balance) AS total_liquid_assets
            FROM user_financial_accounts ufa
            INNER JOIN financial_accounts fa ON ufa.financial_account_id = fa.id
            WHERE fa.is_liquid = 1
              AND fa.is_active = 1
              AND ufa.is_active = 1
              AND fa.type = 'AS'
        ";

        $bindings = [];

        if ($user_account_id !== null) {
            $sql .= " AND ufa.user_account_id = ? ";
            $bindings[] = $user_account_id;
        }

        $sql .= " GROUP BY ufa.user_account_id ORDER BY total_liquid_assets DESC ";

        return DB::select($sql, $bindings);
    }

    public static function getAdminLiquidAssetsSummary(): array
    {
        $baseWhere = "
            fa.is_liquid = 1
            AND fa.is_active = 1
            AND ufa.is_active = 1
            AND fa.type = 'AS'
        ";

        $totalAll = DB::selectOne("
            SELECT COALESCE(SUM(ufa.balance), 0) AS total_all
            FROM user_financial_accounts ufa
            INNER JOIN financial_accounts fa ON ufa.financial_account_id = fa.id
            WHERE {$baseWhere}
        ");

        $byUser = DB::select("
            SELECT ufa.user_account_id, SUM(ufa.balance) AS total_liquid_assets
            FROM user_financial_accounts ufa
            INNER JOIN financial_accounts fa ON ufa.financial_account_id = fa.id
            WHERE {$baseWhere}
            GROUP BY ufa.user_account_id
            ORDER BY total_liquid_assets DESC
        ");

        $byAccount = DB::select("
            SELECT 
                fa.id AS financial_account_id,
                fa.name,
                fa.type,
                SUM(ufa.balance) AS total_liquid_assets
            FROM user_financial_accounts ufa
            INNER JOIN financial_accounts fa ON ufa.financial_account_id = fa.id
            WHERE {$baseWhere}
            GROUP BY fa.id, fa.name, fa.type
            ORDER BY total_liquid_assets DESC
        ");

        return [
            'total_all' => (int) ($totalAll->total_all ?? 0),
            'by_user' => array_map(fn ($r) => [
                'user_account_id' => (int) $r->user_account_id,
                'total_liquid_assets' => (int) $r->total_liquid_assets,
                'formatted' => 'Rp ' . number_format($r->total_liquid_assets, 0, ',', '.'),
            ], $byUser),
            'by_account' => array_map(fn ($r) => [
                'financial_account_id' => (int) $r->financial_account_id,
                'name' => $r->name,
                'type' => $r->type,
                'total_liquid_assets' => (int) $r->total_liquid_assets,
                'formatted' => 'Rp ' . number_format($r->total_liquid_assets, 0, ',', '.'),
            ], $byAccount),
        ];
    }
}
