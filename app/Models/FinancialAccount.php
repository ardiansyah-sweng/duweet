<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\FinancialAccountColumns;
use Illuminate\Support\Facades\DB;

class FinancialAccount extends Model
{
    use HasFactory;
    protected $table;

    protected $fillable = [
        'name', 'type', 'balance', 'initial_balance',
        'is_group', 'description', 'is_active'
    ];

    public $timestamps = true;

    protected $casts = [
        FinancialAccountColumns::BALANCE => 'integer',
        FinancialAccountColumns::INITIAL_BALANCE => 'integer',
        FinancialAccountColumns::IS_GROUP => 'boolean',
        FinancialAccountColumns::IS_ACTIVE => 'boolean',
    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.financial_account', 'financial_accounts');
    }

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
        return $this->hasMany(Transaction::class, 'financial_account_id');
    }

    public function userFinancialAccounts()
    {
        return $this->hasMany(UserFinancialAccount::class, 'financial_account_id');
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