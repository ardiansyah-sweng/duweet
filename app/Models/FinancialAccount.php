<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\FinancialAccountColumns;
use Illuminate\Support\Facades\DB;

class FinancialAccount extends Model
{
    use HasFactory;
>>>>>>> 2e6795b85b7c600fcd326f22537e737dd96beb55

    protected $table;

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