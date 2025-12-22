<?php

namespace App\Models;

use App\Constants\FinancialAccountColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $table;

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

    /**
     * Nama tabel diambil dari config/db_tables.php
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.financial_account', 'financial_accounts');
    }

    /**
     * Relasi ke parent account (self-referential)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, FinancialAccountColumns::PARENT_ID);
    }

    /**
     * Relasi ke children accounts (self-referential)
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, FinancialAccountColumns::PARENT_ID);
    }

    /**
     * Relasi ke transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'financial_account_id');
    }

    /**
     * Relasi ke UserFinancialAccount
     */
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

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $result = DB::select($sql, [$id]);
        return !empty($result) ? $result[0] : null;
    }
}
