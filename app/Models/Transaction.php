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
                t.entry_type, -- Ini akan 'debit' (jika EX) or 'credit' (jika IN)
                
                -- Menggabungkan nama dari tabel 'users'
                CONCAT_WS(' ', u.nama_awal, u.nama_tengah, u.nama_akhir) as user_name,
                
                -- Mengambil nama dari rekening KATEGORI (bukan dompet/bank)
                a.name as category_name,
                a.type as category_type
            FROM
                transactions as t
            JOIN
                users as u ON t.user_id = u.id
            JOIN
                -- GANTI 'accounts' menjadi 'financial_accounts'
                financial_accounts as a ON t.account_id = a.id
            WHERE
                t.created_at >= NOW() - INTERVAL 7 DAY
                
                -- FILTER KUNCI:
                -- Hanya tampilkan entri yang merupakan KATEGORI 
                -- (Pemasukan/Pengeluaran), BUKAN rekening Aset/Utang.
                AND a.type IN ('IN', 'EX', 'SP') 
                
            ORDER BY
                t.created_at DESC
            LIMIT 20 
        ";

        return DB::select($query);
    }


}