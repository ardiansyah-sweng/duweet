<?php

namespace App\Models;

use App\Constants\FinancialAccountColumns;
use App\Constants\TransactionColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class FinancialAccount extends Model
{
    use HasFactory;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.financial_account', 'financial_accounts');
    }

    public $timestamps = false;

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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, FinancialAccountColumns::PARENT_ID);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, FinancialAccountColumns::PARENT_ID);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, TransactionColumns::FINANCIAL_ACCOUNT_ID, FinancialAccountColumns::ID);
    }

    public function userFinancialAccounts(): HasMany
    {
        return $this->hasMany(UserFinancialAccount::class);
    }

    protected static function booted()
    {
        static::deleting(function ($account) {
            if ($account->{FinancialAccountColumns::IS_GROUP} && $account->children()->exists()) {
                throw new \Exception('Tidak dapat menghapus akun grup yang masih memiliki akun turunan.');
            }

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