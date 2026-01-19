<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\FinancialAccountColumns; // Constant yang Anda berikan
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class FinancialAccount extends Model
{
    use HasFactory;
    use HasFactory; // Diletakkan di awal body class

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
        FinancialAccountColumns::PARENT_ID,
        FinancialAccountColumns::NAME,
        FinancialAccountColumns::TYPE,
        FinancialAccountColumns::BALANCE,
        FinancialAccountColumns::IS_ACTIVE,
        FinancialAccountColumns::INITIAL_BALANCE,
        FinancialAccountColumns::IS_GROUP,
        FinancialAccountColumns::DESCRIPTION,
        FinancialAccountColumns::SORT_ORDER,
        FinancialAccountColumns::LEVEL,
    ];

    protected $casts = [
        // Menggunakan Constant untuk 'is_active' dan menambahkan casts yang umum (misal: balance)
        FinancialAccountColumns::BALANCE => 'integer',
        FinancialAccountColumns::INITIAL_BALANCE => 'integer',
        FinancialAccountColumns::IS_GROUP => 'boolean',
        FinancialAccountColumns::IS_ACTIVE => 'boolean',
    ];

    public $timestamps = true;

    /**
     * Relasi ke Parent Account
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
            if ($account->{FinancialAccountColumns::IS_GROUP} && $account->children()->exists()) {
                throw new \Exception('Tidak dapat menghapus akun grup yang masih memiliki akun turunan.');
            }

            // Tidak boleh menghapus akun leaf yang masih punya transaksi
            if (!$account->{FinancialAccountColumns::IS_GROUP} && $account->transactions()->exists()) {
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

public static function getActiveAccounts()
{
    $instance = new self();
    $tableName = $instance->getTable();
    
    // Gunakan query SQL biasa sesuai tugas Anda
    $sql = "SELECT * FROM {$tableName} WHERE is_active = ?";
    
    return DB::select($sql, [1]); 
}

    public function getById($id){
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $result = DB::select($sql, [$id]);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * ================== SOFT DELETE METHODS ==================
     * Query raw untuk soft delete (set status inactive)
     */

    /**
     * Soft delete single account by ID
     * Mengubah status is_active menjadi 0 (inactive)
     * 
     * @param int $id ID dari financial account
     * @return int Jumlah baris yang di-update
     */
    public static function softDeleteById(int $id): int
    {
        return DB::update(
            "UPDATE financial_accounts SET is_active = 0, updated_at = ? WHERE id = ?",
            [now(), $id]
        );
    }

    /**
     * Soft delete multiple accounts by IDs
     * 
     * @param array $ids Array of financial account IDs
     * @return int Jumlah baris yang di-update
     */
    public static function softDeleteByIds(array $ids): int
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $params = array_merge([now()], $ids);
        
        return DB::update(
            "UPDATE financial_accounts SET is_active = 0, updated_at = ? WHERE id IN ({$placeholders})",
            $params
        );
    }

    /**
     * Restore dari soft delete (set status kembali ke active)
     * 
     * @param int $id ID dari financial account yang akan di-restore
     * @return int Jumlah baris yang di-update
     */
    public static function restoreById(int $id): int
    {
        return DB::update(
            "UPDATE financial_accounts SET is_active = 1, updated_at = ? WHERE id = ?",
            [now(), $id]
        );
    }

    /**
     * Get hanya active accounts menggunakan raw query
     * 
     * @return array Array of active financial accounts
     */
    public static function getActiveAccountsOnly(): array
    {
        return DB::select(
            "SELECT * FROM financial_accounts WHERE is_active = 1 ORDER BY sort_order ASC"
        );
    }

    /**
     * Get hanya inactive accounts (yang sudah di-soft-delete)
     * 
     * @return array Array of inactive financial accounts
     */
    public static function getInactiveAccounts(): array
    {
        return DB::select(
            "SELECT * FROM financial_accounts WHERE is_active = 0 ORDER BY updated_at DESC"
        );
    }

    /**
     * Check apakah account sudah soft-deleted (inactive)
     * 
     * @param int $id ID dari financial account
     * @return bool true jika inactive, false jika active
     */
    public static function isInactive(int $id): bool
    {
        $result = DB::selectOne(
            "SELECT id FROM financial_accounts WHERE id = ? AND is_active = 0",
            [$id]
        );
        return $result !== null;
    }

}