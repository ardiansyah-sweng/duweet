<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\TransactionEntryType;
use App\Enums\TransactionBalanceEffect;
// Asumsi Anda punya model UserAccount
use App\Models\UserAccount; 

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $casts = [
        'entry_type'     => TransactionEntryType::class,
        'balance_effect' => TransactionBalanceEffect::class,
        'amount'         => 'integer',
        'is_balance'     => 'boolean',
    ];

    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id', 'id');
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id', 'id');
    }

    public static function getLatestActivitiesRaw()
    {
        $query = "
            SELECT
                t.amount,
                t.description,
                t.created_at,
                t.entry_type, 
                
                -- Mengambil 'username' dari tabel 'user_accounts'
                ua.username as user_name,
                
                -- Mengambil nama dari rekening KATEGORI
                a.name as category_name,
                a.type as category_type
            FROM
                transactions as t
            JOIN
                -- PERBAIKAN 1:
                -- Sambungkan ke 'user_accounts' (ua)
                -- menggunakan 't.user_account_id' (dari transactions)
                -- dan 'ua.id' (primary key dari user_accounts)
                user_accounts as ua ON t.user_account_id = ua.id
            JOIN
                -- PERBAIKAN 2:
                -- Sambungkan ke 'financial_accounts' (a)
                -- menggunakan 't.financial_account_id' (dari transactions)
                -- dan 'a.id' (primary key dari financial_accounts)
                financial_accounts as a ON t.financial_account_id = a.id
            WHERE
                t.created_at >= NOW() - INTERVAL 7 DAY
                
                -- Filter ini tetap sama
                AND a.type IN ('IN', 'EX', 'SP') 
                
            ORDER BY
                t.created_at DESC
            LIMIT 20
        ";

        return DB::select($query);
    }
}