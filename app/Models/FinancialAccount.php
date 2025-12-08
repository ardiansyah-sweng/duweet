<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Constants\FinancialAccountColumns;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $table = 'financial_accounts';

    protected $fillable = [
        FinancialAccountColumns::NAME,
        FinancialAccountColumns::PARENT_ID,
        FinancialAccountColumns::TYPE,
        FinancialAccountColumns::BALANCE,
        FinancialAccountColumns::INITIAL_BALANCE,
        FinancialAccountColumns::DESCRIPTION,
        FinancialAccountColumns::IS_GROUP,
        FinancialAccountColumns::IS_ACTIVE,
        FinancialAccountColumns::SORT_ORDER,
        FinancialAccountColumns::LEVEL,
    ];

    protected $casts = [
        FinancialAccountColumns::BALANCE => 'integer',
        FinancialAccountColumns::INITIAL_BALANCE => 'integer',
        FinancialAccountColumns::IS_GROUP => 'boolean',
        FinancialAccountColumns::IS_ACTIVE => 'boolean',
    ];

    public $timestamps = true;

    public function parent()
    {
        return $this->belongsTo(self::class, FinancialAccountColumns::PARENT_ID);
    }

    /**
     * Factory method untuk membuat akun keuangan sekaligus relasi UserFinancialAccount.
     * Menerapkan sebagian business rules dari PRD:
     * - name: required
     * - type: harus salah satu enum valid
     * - initial_balance & balance: >= 0 untuk ASSET, boleh negatif untuk LI jika diperlukan (di sini tetap >=0 untuk sederhana)
     * - parent harus sama type jika diberikan (validasi sederhana di sini)
     * - is_group true => balance & initial_balance dipaksa 0
     */
    public static function createForUser(array $data): self
    {
        $required = ['user_id','name','type','initial_balance'];
        foreach ($required as $field) {
            if (!array_key_exists($field, $data)) {
                throw new \InvalidArgumentException("Missing field: {$field}");
            }
        }

        $validTypes = ['IN','EX','SP','LI','AS'];
        if (!in_array($data['type'], $validTypes, true)) {
            throw new \InvalidArgumentException('Invalid account type');
        }

        $isGroup = (bool)($data['is_group'] ?? false);
        $initial = (int) $data['initial_balance'];

        if ($isGroup) {
            $initial = 0; // group tidak menyimpan saldo langsung
        } elseif ($data['type'] === 'AS' && $initial < 0) {
            throw new \InvalidArgumentException('Asset balance cannot be negative');
        }

        // Parent type check (simplified) dengan raw DML SELECT
        if (!empty($data['parent_id'])) {
            $parent = DB::selectOne(
                "SELECT type FROM financial_accounts WHERE id = ? LIMIT 1",
                [$data['parent_id']]
            );
            if ($parent && $parent->type !== $data['type']) {
                throw new \InvalidArgumentException('Parent and child must share the same type');
            }
        }
        // Gunakan transaction + DML (raw SQL) agar atomic & sesuai permintaan
        return DB::transaction(function () use ($data, $isGroup, $initial) {
            $now = now();
            DB::insert(
                "INSERT INTO financial_accounts (name, type, balance, initial_balance, is_group, description, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $data['name'],
                    $data['type'],
                    $initial,
                    $initial,
                    $isGroup ? 1 : 0,
                    $data['description'] ?? null,
                    1,
                    $now,
                    $now,
                ]
            );

            $accountId = (int) DB::getPdo()->lastInsertId();

            if (!$isGroup) {
                DB::insert(
                    "INSERT INTO user_financial_accounts (user_id, financial_account_id, balance, initial_balance, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [
                        $data['user_id'],
                        $accountId,
                        $initial,
                        $initial,
                        1,
                        $now,
                        $now,
                    ]
                );
            }

            // Kembalikan instance model tanpa query Eloquent (hydrate manual)
            $row = DB::selectOne(
                "SELECT id, name, type, balance, initial_balance, is_group, description, is_active, created_at, updated_at FROM financial_accounts WHERE id = ? LIMIT 1",
                [$accountId]
            );

            $fa = new self();
            foreach ((array)$row as $key => $value) {
                $fa->setAttribute($key, $value);
            }
            $fa->exists = true;
            return $fa;
        });
    }

    /**
     * DML Query INSERT untuk membuat Financial Account menggunakan Query Builder.
     * Method ini mendemonstrasikan penggunaan raw DML query insert.
     * 
     * @param array $data Data untuk insert financial account
     * @return int ID dari financial account yang baru dibuat
     */
    public static function insertFinancialAccount(array $data): int
    {
        // Validasi required fields
        $required = ['name', 'type', 'initial_balance'];
        foreach ($required as $field) {
            if (!array_key_exists($field, $data)) {
                throw new \InvalidArgumentException("Missing field: {$field}");
            }
        }

        // Validasi type
        $validTypes = ['IN', 'EX', 'SP', 'LI', 'AS'];
        if (!in_array($data['type'], $validTypes, true)) {
            throw new \InvalidArgumentException('Invalid account type: ' . $data['type']);
        }

        $isGroup = (bool)($data['is_group'] ?? false);
        $balance = $isGroup ? 0 : (int)$data['initial_balance'];

        // DML Query INSERT menggunakan raw SQL
        $now = now();
        DB::insert(
            "INSERT INTO financial_accounts (name, type, balance, initial_balance, is_group, description, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['type'],
                $balance,
                $balance,
                $isGroup ? 1 : 0,
                $data['description'] ?? null,
                ($data['is_active'] ?? true) ? 1 : 0,
                $now,
                $now,
            ]
        );

        return (int) DB::getPdo()->lastInsertId();
    }

    /**
     * DML Query INSERT untuk membuat relasi User-Financial Account (pivot table).
     * 
     * @param int $userId ID user
     * @param int $financialAccountId ID financial account
     * @param int $balance Saldo awal
     * @return bool Success status
     */
    public static function insertUserFinancialAccount(int $userId, int $financialAccountId, int $balance): bool
    {
        // DML Query INSERT untuk pivot table (raw SQL)
        $now = now();
        return DB::insert(
            "INSERT INTO user_financial_accounts (user_id, financial_account_id, balance, initial_balance, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $userId,
                $financialAccountId,
                $balance,
                $balance,
                1,
                $now,
                $now,
            ]
        );
    }

    /**
     * DML Query INSERT lengkap: membuat financial account dan relasinya dalam satu transaksi.
     * Method ini menggunakan raw DML query insert, bukan Eloquent.
     * 
     * @param array $data Data untuk create financial account dengan user
     * @return array ['account_id' => int, 'pivot_created' => bool]
     */
    public static function insertFinancialAccountWithUser(array $data): array
    {
        // Validasi
        if (!isset($data['user_id'])) {
            throw new \InvalidArgumentException("Missing field: user_id");
        }

        // Gunakan transaction untuk atomic operation
        return DB::transaction(function () use ($data) {
            // 1. Insert financial account menggunakan DML query
            $accountId = self::insertFinancialAccount($data);

            // 2. Insert pivot table jika bukan group account
            $pivotCreated = false;
            $isGroup = (bool)($data['is_group'] ?? false);
            
            if (!$isGroup) {
                $balance = (int)$data['initial_balance'];
                $pivotCreated = self::insertUserFinancialAccount(
                    $data['user_id'],
                    $accountId,
                    $balance
                );
            }

            return [
                'account_id'    => $accountId,
                'pivot_created' => $pivotCreated,
            ];
        });
    }

    /**
     * DML Query SUM untuk menghitung total liquid asset user.
     * Query ini menggunakan DB facade untuk mendapatkan sum dari balance.
     * 
     * Liquid asset = Account dengan tipe AS (Asset) atau LI (Liability) 
     * yang aktif dan bukan group account.
     * 
     * @param int $userId ID user yang akan dihitung liquid asset-nya
     * @param array $options Filter options (type, include_inactive, min_balance)
     * @return int Total liquid asset dalam integer
     */
    public static function sumLiquidAssetByUser(int $userId, array $options = []): int
    {
        // Bangun raw SELECT SUM dengan parameter binding manual
        $types = $options['type'] ?? ['AS', 'LI'];
        if (is_string($types)) {
            $types = [$types];
        }

        $sql = "SELECT SUM(ufa.balance) AS total \n FROM user_financial_accounts ufa \n JOIN financial_accounts fa ON fa.id = ufa.financial_account_id \n WHERE ufa.user_id = ? AND fa.is_group = 0";
        $bindings = [$userId];

        // Filter type IN (...)
        if (!empty($types)) {
            $placeholders = implode(',', array_fill(0, count($types), '?'));
            $sql .= " AND fa.type IN ($placeholders)";
            $bindings = array_merge($bindings, $types);
        }

        // Active filter
        if (empty($options['include_inactive'])) {
            $sql .= " AND ufa.is_active = 1";
        }

        // Min balance
        if (isset($options['min_balance'])) {
            $sql .= " AND ufa.balance >= ?";
            $bindings[] = (int)$options['min_balance'];
        }

        $row = DB::selectOne($sql, $bindings);
        return (int)($row->total ?? 0);
    }

    /**
     * DML Query untuk mendapatkan detail liquid assets user.
     * Method ini mengembalikan list account beserta balance-nya.
     * 
     * @param int $userId ID user
     * @param array $options Filter options
     * @return \Illuminate\Support\Collection Collection of liquid accounts
     */
    public static function getLiquidAssetDetailsByUser(int $userId, array $options = []): \Illuminate\Support\Collection
    {
        // Raw SELECT dengan JOIN + dynamic filters
        $types = $options['type'] ?? ['AS', 'LI'];
        if (is_string($types)) { $types = [$types]; }

        $sql = "SELECT fa.id, fa.name, fa.type, fa.description, ufa.balance, ufa.initial_balance, ufa.is_active\n                FROM user_financial_accounts ufa\n                JOIN financial_accounts fa ON fa.id = ufa.financial_account_id\n                WHERE ufa.user_id = ? AND fa.is_group = 0";
        $bindings = [$userId];

        if (!empty($types)) {
            $placeholders = implode(',', array_fill(0, count($types), '?'));
            $sql .= " AND fa.type IN ($placeholders)";
            $bindings = array_merge($bindings, $types);
        }
        if (empty($options['include_inactive'])) {
            $sql .= " AND ufa.is_active = 1";
        }
        if (isset($options['min_balance'])) {
            $sql .= " AND ufa.balance >= ?";
            $bindings[] = (int) $options['min_balance'];
        }
        $sql .= " ORDER BY ufa.balance DESC";

        $rows = DB::select($sql, $bindings);
        return collect($rows);
    }

    /**
     * DML Query untuk mendapatkan summary liquid asset per type.
     * 
     * @param int $userId ID user
     * @return array ['AS' => total, 'LI' => total, 'total' => total]
     */
    public static function getLiquidAssetSummaryByUser(int $userId): array
    {
        // Raw SELECT dengan GROUP BY untuk summary per type
        $sql = "SELECT fa.type, SUM(ufa.balance) AS total, COUNT(*) AS count\n                FROM user_financial_accounts ufa\n                JOIN financial_accounts fa ON fa.id = ufa.financial_account_id\n                WHERE ufa.user_id = ? AND fa.is_group = 0 AND ufa.is_active = 1 AND fa.type IN ('AS','LI')\n                GROUP BY fa.type";
        $summary = DB::select($sql, [$userId]);

        // Format result
        $result = [
            'AS' => 0,
            'LI' => 0,
            'total' => 0,
            'details' => [],
        ];

        foreach ($summary as $row) {
            $result[$row->type] = (int) $row->total;
            $result['total'] += (int) $row->total;
            $result['details'][$row->type] = [
                'total' => (int) $row->total,
                'count' => (int) $row->count,
            ];
        }

        return $result;
    }

    /**
     * Get pivot row for a specific user and account
     * @param int $accountId
     * @param int $userId
     * @return object|null
     */
    public static function getUserPivot(int $accountId, int $userId)
    {
        $row = DB::selectOne(
            "SELECT * FROM user_financial_accounts WHERE financial_account_id = ? AND user_id = ? LIMIT 1",
            [$accountId, $userId]
        );
        return $row;
    }

    /**
     * List financial accounts with optional user balances joined
     * @param int|null $userId
     * @return \Illuminate\Support\Collection
     */
    public static function listWithUserBalances(?int $userId = null)
    {
        $sql = "SELECT fa.id, fa.name, fa.type, fa.balance, fa.initial_balance, fa.description, fa.is_active, fa.created_at,\n                        ufa.user_id, ufa.balance AS user_balance, ufa.initial_balance AS user_initial_balance\n                FROM financial_accounts fa\n                LEFT JOIN user_financial_accounts ufa ON ufa.financial_account_id = fa.id";
        $bindings = [];
        if (!is_null($userId)) {
            $sql .= " WHERE ufa.user_id = ?";
            $bindings[] = $userId;
        }
        $sql .= " ORDER BY fa.id DESC";
        $rows = DB::select($sql, $bindings);
        return collect($rows);
    }
    
    /**
     * Find account with joined user pivot (if exists)
     * @param int $id
     * @return object|null
     */
    public static function findWithUserBalance(int $id)
    {
        $row = DB::selectOne(
            "SELECT fa.id, fa.name, fa.type, fa.balance, fa.initial_balance, fa.description, fa.is_active, fa.created_at,\n                    ufa.user_id, ufa.balance AS user_balance, ufa.initial_balance AS user_initial_balance\n             FROM financial_accounts fa\n             LEFT JOIN user_financial_accounts ufa ON ufa.financial_account_id = fa.id\n             WHERE fa.id = ? LIMIT 1",
            [$id]
        );
        return $row;
    }
}