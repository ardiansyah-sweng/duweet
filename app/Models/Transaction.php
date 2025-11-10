<?php

namespace App\Models;


// 1. IMPORT CLASS YANG DIBUTUHKAN
use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // WAJIB import ini

class Transaction extends Model
{
    use HasFactory;

    /**
     * INI ADALAH FUNGSI VERSI QUERY MURNI (RAW SQL)
     * * Mengambil total pengeluaran (expenses) berdasarkan periode.
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array
     */
    public static function getExpensesByPeriodRaw($startDate, $endDate)
    {
        // 2. Siapkan query murninya
        $sql = "
            SELECT
                YEAR(t.transaction_date) AS tahun,
                MONTH(t.transaction_date) AS bulan,
                SUM(t.amount) AS total_pengeluaran
            FROM
                transactions AS t
            JOIN
                financial_accounts AS fa ON t.financial_account_id = fa.id
            WHERE
                -- Filter: Hanya akun tipe 'EX' (Expenses)
                fa.type = ?
                
                -- Filter: Hanya transaksi 'debit' di akun 'EX'
                AND t.entry_type = ?
                
                -- Filter: Berdasarkan rentang tanggal
                AND t.transaction_date BETWEEN ? AND ?
            GROUP BY
                tahun, bulan
            ORDER BY
                tahun ASC, bulan ASC;
        ";

        // 3. Siapkan binding untuk keamanan (anti-SQL Injection)
        // Kita pakai Enum yang sudah kamu buat
        $bindings = [
            AccountType::EXPENSES->value, // 'EX'
            'debit',
            $startDate,
            $endDate
        ];

        // 4. Eksekusi query murni
        $results = DB::select($sql, $bindings);

        return $results;
    }
}