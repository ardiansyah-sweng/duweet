<?php

<<<<<<< HEAD
// app/Models/FinancialAccount.php
namespace App\Models;

// Import konstanta Anda jika ada, atau tulis manual
use App\Constants\AccountColumns;
use App\Constants\FinancialAccountColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
=======
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\FinancialAccountColumns;
>>>>>>> origin/main

class FinancialAccount extends Model
{
    use HasFactory;

<<<<<<< HEAD
    /**
     * Tentukan nama tabel secara eksplisit
     */

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
        FinancialAccountColumns::PARENT_ID,
        FinancialAccountColumns::NAME,
        FinancialAccountColumns::TYPE,
        FinancialAccountColumns::BALANCE,
        FinancialAccountColumns::INITIAL_BALANCE,
        FinancialAccountColumns::IS_GROUP,
        FinancialAccountColumns::DESCRIPTION,
=======
    protected $fillable = [
        FinancialAccountColumns::NAME,
        FinancialAccountColumns::PARENT_ID,
        FinancialAccountColumns::TYPE,
        FinancialAccountColumns::BALANCE,
        FinancialAccountColumns::INITIAL_BALANCE,
        FinancialAccountColumns::DESCRIPTION,
        FinancialAccountColumns::IS_GROUP,
>>>>>>> origin/main
        FinancialAccountColumns::IS_ACTIVE,
        FinancialAccountColumns::SORT_ORDER,
        FinancialAccountColumns::LEVEL,
    ];
<<<<<<< HEAD
=======

        protected $casts = [
            FinancialAccountColumns::BALANCE => 'integer',
            FinancialAccountColumns::INITIAL_BALANCE => 'integer',
            FinancialAccountColumns::IS_GROUP => 'boolean',
            FinancialAccountColumns::IS_ACTIVE => 'boolean',
        ];

        public function parent()
        {
            return $this->belongsTo(self::class, FinancialAccountColumns::PARENT_ID);
        }
    protected $casts = [
        FinancialAccountColumns::BALANCE => 'integer',
        FinancialAccountColumns::INITIAL_BALANCE => 'integer',
        FinancialAccountColumns::IS_GROUP => 'boolean',
        FinancialAccountColumns::IS_ACTIVE => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, FinancialAccountColumns::PARENT_ID);
    }
>>>>>>> origin/main
}
