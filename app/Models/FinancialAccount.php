<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\FinancialAccountColumns;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        // Menggunakan Constant untuk 'is_active' dan menambahkan casts yang umum (misal: balance)
        FinancialAccountColumns::BALANCE => 'integer',
        FinancialAccountColumns::INITIAL_BALANCE => 'integer',
        FinancialAccountColumns::IS_GROUP => 'boolean',
        FinancialAccountColumns::IS_ACTIVE => 'boolean',
    ];

    public $timestamps = true;

    /**
     * Relasi ke Parent Account
     */
    public function parent()
    {
        return $this->belongsTo(self::class, FinancialAccountColumns::PARENT_ID);
    }

    public function children()
    {
        return $this->hasMany(self::class, FinancialAccountColumns::PARENT_ID);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, FinancialAccountColumns::ID);
    }

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
        
        $sql = "SELECT * FROM {$tableName} WHERE is_active = ?";
        
        return DB::select($sql, [1]);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $result = DB::select($sql, [$id]);
        return !empty($result) ? $result[0] : null;
    }

}