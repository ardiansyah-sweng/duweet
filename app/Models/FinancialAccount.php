<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Constants\FinancialAccountColumns;


class FinancialAccount extends Model
{
    use HasFactory;

    protected $table;

    /**
     * Nama tabel diambil dari config/db_tables.php
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.financial_account', 'financial_accounts');
    }

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

    /**
     * Relasi ke parent account
     */
    public function parent()
    {
        return $this->belongsTo(self::class, FinancialAccountColumns::PARENT_ID);
    }

    /**
     * Relasi ke child accounts
     */
    public function children()
    {
        return $this->hasMany(self::class, FinancialAccountColumns::PARENT_ID);
    }

    /**
     * Relasi ke transaksi (leaf accounts)
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, FinancialAccountColumns::ID);
    }

    // Relasi ke UserFinancialAccount
    public function userFinancialAccounts()
    {
        return $this->hasMany(UserFinancialAccount::class);
    }


    /**
     * Aturan dasar sebelum delete (mencegah kehilangan data penting)
     */
    protected static function booted()
    {
        static::deleting(function ($account) {
            // Tidak boleh menghapus akun grup jika masih punya anak
            if ($account->{AccountColumns::IS_GROUP} && $account->children()->exists()) {
                throw new \Exception('Tidak dapat menghapus akun grup yang masih memiliki akun turunan.');
            }

            // Tidak boleh menghapus akun leaf yang masih punya transaksi
            if (!$account->{AccountColumns::IS_GROUP} && $account->transactions()->exists()) {
                throw new \Exception('Tidak dapat menghapus akun yang masih memiliki transaksi.');
            }
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
     * DML Query SUM untuk menghitung total liquid asset berdasarkan user_account_id.
     * Query ini menggunakan user_account_id sebagai filter, bukan user_id.
     * 
     * Liquid asset = Account dengan tipe AS (Asset) atau LI (Liability) 
     * yang aktif dan bukan group account.
     * 
     * @param int $userAccountId ID dari user_account
     * @param array $options Filter options (type, include_inactive, min_balance)
     * @return int Total liquid asset dalam integer
     */
    public static function sumLiquidAssetByUserAccount(int $userAccountId, array $options = []): int
    {
        // Bangun raw SELECT SUM dengan parameter binding
        $types = $options['type'] ?? ['AS', 'LI'];
        if (is_string($types)) {
            $types = [$types];
        }

        $sql = "SELECT SUM(ufa.balance) AS total \n FROM user_financial_accounts ufa \n JOIN financial_accounts fa ON fa.id = ufa.financial_account_id \n WHERE ufa.user_account_id = ? AND fa.is_group = 0";
        $bindings = [$userAccountId];

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

    public function getById($id){
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $result = DB::select($sql, [$id]);
        return !empty($result) ? $result[0] : null;
    }

}
