<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FinancialAccount extends Model
{
    // ... code existing ...

    /**
     * TUGAS 2: Query Sum Balance dari Child Accounts
     * Menghitung total saldo milik Parent Account berdasarkan anak-anaknya
     */
    public static function getParentBalanceRaw($parentId)
    {
        // Mengecek apakah akun ini adalah parent (is_group)
        // dan menjumlahkan balance akun-akun yang parent_id-nya adalah $parentId
        
        $sql = "
            SELECT 
                parent.id as parent_id,
                parent.name as parent_name,
                COALESCE(SUM(child.balance), 0) as total_child_balance
            FROM financial_accounts as parent
            LEFT JOIN financial_accounts as child ON child.parent_id = parent.id
            WHERE parent.id = ?
            GROUP BY parent.id, parent.name
        ";

        $result = DB::select($sql, [$parentId]);

        return !empty($result) ? $result[0] : null;
    }
}