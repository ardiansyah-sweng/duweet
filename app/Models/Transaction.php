<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\TransactionEntryType;   // <-- Import Enum
use App\Enums\TransactionBalanceEffect; // <-- Import Enum

class Transaction extends Model
{
    use HasFactory;

 
    protected $table = 'transactions';

    protected $fillable = [
        'transaction_group_id',
        'user_id',
        'account_id',
        'entry_type',
        'amount',
        'balance_effect',
        'description',
        'is_balance',
    ];

    protected $casts = [
        'entry_type'     => TransactionEntryType::class,
        'balance_effect' => TransactionBalanceEffect::class,
        'amount'         => 'integer',
        'is_balance'     => 'boolean',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function financialAccount()
    {
  
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }

    public static function getLatestActivitiesRaw()
    {
        $query = "
            SELECT
                t.amount,
                t.description,
                t.created_at,
                t.entry_type, 
                
                -- PERUBAHAN 1:
                -- Mengambil 'username' dari tabel 'user_accounts'
                ua.username as user_name,
                
                -- Mengambil nama dari rekening KATEGORI
                a.name as category_name,
                a.type as category_type
            FROM
                transactions as t
            JOIN
                -- PERUBAHAN 2:
                -- Mengganti JOIN 'users' menjadi 'user_accounts'
                -- Kita beri alias 'ua' (untuk User Account)
                user_accounts as ua ON t.user_id = ua.user_id
            JOIN
                -- Ini tetap sama
                financial_accounts as a ON t.account_id = a.id
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