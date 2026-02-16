<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    // ... code existing ...

    /**
     * TUGAS 1: Query Saldo Berjalan (Running Balance)
     * Menggunakan Window Function (MySQL 8.0+)
     */
    public static function getTransactionsWithRunningBalance($accountId)
    {
        // Asumsi: 
        // type 'CR' (Credit) = Uang Masuk (+)
        // type 'DB' (Debit)  = Uang Keluar (-)
        // Sesuaikan 'CR'/'DB' dengan data di database kamu (misal: 'IN'/'OUT')
        
        $sql = "
            SELECT 
                id,
                account_id,
                amount,
                type,
                description,
                created_at,
                SUM(CASE 
                    WHEN type = 'CR' THEN amount 
                    WHEN type = 'DB' THEN -amount 
                    ELSE 0 
                END) OVER (ORDER BY created_at ASC, id ASC) AS running_balance
            FROM transactions
            WHERE account_id = ?
            ORDER BY created_at ASC
        ";

        return DB::select($sql, [$accountId]);
    }
}