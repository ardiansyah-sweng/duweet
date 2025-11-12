<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Constants\TransactionColumns;
use Carbon\Carbon; // Import Carbon untuk type hinting

class Transaction extends Model
{
    use HasFactory;

    // Nama tabel yang sesuai dengan konfigurasi
    protected $table = 'transactions';

    /**
     * Ambil ringkasan total pendapatan berdasarkan periode (Bulan) untuk user tertentu.
     * Ini adalah implementasi dari query: "sum income user by periode".
     * 
     * QUERY DML SQL MURNI:
     * -----------------------------------------------------------
     * SELECT 
     *     DATE_FORMAT(t.created_at, '%Y-%m') AS periode,
     *     COALESCE(SUM(t.amount), 0) AS total_income
     * FROM transactions t
     * INNER JOIN financial_accounts fa 
     *     ON t.financial_account_id = fa.id
     * WHERE 
     *     t.user_account_id = ?
     *     AND fa.type = 'IN'
     *     AND t.balance_effect = 'increase'
     *     AND fa.is_group = 0
     *     AND t.created_at BETWEEN ? AND ?
     * GROUP BY DATE_FORMAT(t.created_at, '%Y-%m')
     * ORDER BY periode ASC;
     * -----------------------------------------------------------
     *
     * @param int $userAccountId
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return array
     */
    public static function getIncomeSummaryByPeriod(int $userAccountId, Carbon $startDate, Carbon $endDate): array
    {
        // DML SQL Murni - Langsung execute raw SQL query
        $sql = "
            SELECT 
                DATE_FORMAT(t.created_at, '%Y-%m') AS periode,
                COALESCE(SUM(t.amount), 0) AS total_income
            FROM transactions t
            INNER JOIN financial_accounts fa 
                ON t.financial_account_id = fa.id
            WHERE 
                t.user_account_id = ?
                AND fa.type = 'IN'
                AND t.balance_effect = 'increase'
                AND fa.is_group = 0
                AND t.created_at BETWEEN ? AND ?
            GROUP BY DATE_FORMAT(t.created_at, '%Y-%m')
            ORDER BY periode ASC
        ";

        // Execute raw SQL dengan parameter binding untuk keamanan
        $results = DB::select($sql, [
            $userAccountId,
            $startDate->toDateTimeString(),
            $endDate->toDateTimeString()
        ]);

        return $results;
    }
}