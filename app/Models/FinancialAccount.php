<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\FinancialAccountColumns as AccountColumns;

class FinancialAccount extends Model
{
    use HasFactory;

    /**
     * Nama tabel diambil dari config/db_tables.php
     */
    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.financial_account'); // sesuai migration
    }

    /**
     * Kolom yang bisa diisi secara mass-assignment
     */
    protected $fillable = [
        AccountColumns::PARENT_ID,
        AccountColumns::NAME,
        AccountColumns::TYPE,
        AccountColumns::BALANCE,
        AccountColumns::INITIAL_BALANCE,
        AccountColumns::IS_GROUP,
        AccountColumns::DESCRIPTION,
        AccountColumns::IS_ACTIVE,
        AccountColumns::SORT_ORDER,
        AccountColumns::LEVEL,
        // color dan icon tidak ada di migration saat ini (belum diaktifkan)
    ];

    /**
     * Casting otomatis tipe data
     */
    protected $casts = [
        AccountColumns::IS_GROUP => 'boolean',
        AccountColumns::IS_ACTIVE => 'boolean',
        AccountColumns::BALANCE => 'integer',
        AccountColumns::INITIAL_BALANCE => 'integer',
        AccountColumns::SORT_ORDER => 'integer',
        AccountColumns::LEVEL => 'integer',
    ];

    /**
     * Relasi ke parent account
     */
    public function parent()
    {
        return $this->belongsTo(FinancialAccount::class, AccountColumns::PARENT_ID);
    }

    /**
     * Relasi ke child accounts
     */
    public function children()
    {
        return $this->hasMany(FinancialAccount::class, AccountColumns::PARENT_ID);
    }

    /**
     * Relasi ke transaksi (leaf accounts)
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id');
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
}