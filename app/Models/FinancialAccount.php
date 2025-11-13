<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FinancialAccount extends Model
{
    protected $table = 'financial_accounts';

    protected $fillable = [
        'name', 'type', 'balance', 'initial_balance',
        'is_group', 'description', 'is_active'
    ];

    public $timestamps = true;

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

        // Parent type check (simplified)
        if (!empty($data['parent_id'])) {
            $parent = self::query()->where('id',$data['parent_id'])->first();
            if ($parent && $parent->type !== $data['type']) {
                throw new \InvalidArgumentException('Parent and child must share the same type');
            }
        }

        $fa = self::create([
            'name'            => $data['name'],
            'type'            => $data['type'],
            'balance'         => $initial,
            'initial_balance' => $initial,
            'is_group'        => $isGroup,
            'description'     => $data['description'] ?? null,
            'is_active'       => true,
        ]);

        // Buat relasi user jika bukan group
        if (!$isGroup) {
            \App\Models\UserFinancialAccount::create([
                'user_id'              => $data['user_id'],
                'financial_account_id' => $fa->id,
                'balance'              => $fa->balance,
                'initial_balance'      => $fa->initial_balance,
                'is_active'            => true,
            ]);
        }

        return $fa;
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

        // DML Query INSERT menggunakan Query Builder
        $accountId = DB::table('financial_accounts')->insertGetId([
            'name'            => $data['name'],
            'type'            => $data['type'],
            'balance'         => $balance,
            'initial_balance' => $balance,
            'is_group'        => $isGroup,
            'description'     => $data['description'] ?? null,
            'is_active'       => $data['is_active'] ?? true,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return $accountId;
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
        // DML Query INSERT untuk pivot table
        $inserted = DB::table('user_financial_accounts')->insert([
            'user_id'              => $userId,
            'financial_account_id' => $financialAccountId,
            'balance'              => $balance,
            'initial_balance'      => $balance,
            'is_active'            => true,
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        return $inserted;
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
        // DML Query SELECT dengan SUM aggregate function
        $query = DB::table('user_financial_accounts as ufa')
            ->join('financial_accounts as fa', 'fa.id', '=', 'ufa.financial_account_id')
            ->where('ufa.user_id', $userId)
            ->where('fa.is_group', false);

        // Filter by account type (default: AS + LI)
        $types = $options['type'] ?? ['AS', 'LI'];
        if (is_string($types)) {
            $query->where('fa.type', $types);
        } else {
            $query->whereIn('fa.type', $types);
        }

        // Filter by active status (default: active only)
        if (empty($options['include_inactive'])) {
            $query->where('ufa.is_active', 1);
        }

        // Filter by minimum balance
        if (isset($options['min_balance'])) {
            $query->where('ufa.balance', '>=', $options['min_balance']);
        }

        // DML Query: SELECT SUM(balance)
        $total = $query->sum('ufa.balance');

        return (int) ($total ?? 0);
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
        // DML Query SELECT dengan JOIN
        $query = DB::table('user_financial_accounts as ufa')
            ->join('financial_accounts as fa', 'fa.id', '=', 'ufa.financial_account_id')
            ->select([
                'fa.id',
                'fa.name',
                'fa.type',
                'fa.description',
                'ufa.balance',
                'ufa.initial_balance',
                'ufa.is_active',
            ])
            ->where('ufa.user_id', $userId)
            ->where('fa.is_group', false);

        // Apply filters (same as sumLiquidAssetByUser)
        $types = $options['type'] ?? ['AS', 'LI'];
        if (is_string($types)) {
            $query->where('fa.type', $types);
        } else {
            $query->whereIn('fa.type', $types);
        }

        if (empty($options['include_inactive'])) {
            $query->where('ufa.is_active', 1);
        }

        if (isset($options['min_balance'])) {
            $query->where('ufa.balance', '>=', $options['min_balance']);
        }

        // Order by balance descending
        $query->orderByDesc('ufa.balance');

        return $query->get();
    }

    /**
     * DML Query untuk mendapatkan summary liquid asset per type.
     * 
     * @param int $userId ID user
     * @return array ['AS' => total, 'LI' => total, 'total' => total]
     */
    public static function getLiquidAssetSummaryByUser(int $userId): array
    {
        // DML Query dengan GROUP BY untuk summary per type
        $summary = DB::table('user_financial_accounts as ufa')
            ->join('financial_accounts as fa', 'fa.id', '=', 'ufa.financial_account_id')
            ->select([
                'fa.type',
                DB::raw('SUM(ufa.balance) as total'),
                DB::raw('COUNT(*) as count'),
            ])
            ->where('ufa.user_id', $userId)
            ->where('fa.is_group', false)
            ->where('ufa.is_active', 1)
            ->whereIn('fa.type', ['AS', 'LI'])
            ->groupBy('fa.type')
            ->get();

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
        return DB::table('user_financial_accounts')
            ->where('financial_account_id', $accountId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * List financial accounts with optional user balances joined
     * @param int|null $userId
     * @return \Illuminate\Support\Collection
     */
    public static function listWithUserBalances(?int $userId = null)
    {
        $q = DB::table('financial_accounts as fa')
            ->leftJoin('user_financial_accounts as ufa', 'ufa.financial_account_id', '=', 'fa.id')
            ->select(
                'fa.id','fa.name','fa.type','fa.balance','fa.initial_balance',
                'fa.description','fa.is_active','fa.created_at',
                'ufa.user_id','ufa.balance as user_balance','ufa.initial_balance as user_initial_balance'
            )
            ->orderByDesc('fa.id');

        if (!is_null($userId)) {
            $q->where('ufa.user_id', $userId);
        }

        return $q->get();
    }

    /**
     * Find account with joined user pivot (if exists)
     * @param int $id
     * @return object|null
     */
    public static function findWithUserBalance(int $id)
    {
        return DB::table('financial_accounts as fa')
            ->leftJoin('user_financial_accounts as ufa', 'ufa.financial_account_id', '=', 'fa.id')
            ->select(
                'fa.id','fa.name','fa.type','fa.balance','fa.initial_balance',
                'fa.description','fa.is_active','fa.created_at',
                'ufa.user_id','ufa.balance as user_balance','ufa.initial_balance as user_initial_balance'
            )
            ->where('fa.id', $id)
            ->first();
    }
}