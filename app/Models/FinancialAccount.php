<?php

// app/Models/FinancialAccount.php
namespace App\Models;

// Import konstanta Anda jika ada, atau tulis manual
use App\Constants\AccountColumns; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialAccount extends Model
{
    use HasFactory;

    /**
     * Tentukan nama tabel secara eksplisit
     */
    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        // Set nama tabel dari config
        $this->table = config('db_tables.financial_account');
    }

    /**
     * Kolom yang boleh diisi
     * Sesuaikan dengan nama konstanta Anda
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
    ];
}
