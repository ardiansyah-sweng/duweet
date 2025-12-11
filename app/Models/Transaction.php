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
     * ... (Metode getIncomeSummaryByPeriod yang sudah ada)
     */
    public static function getIncomeSummaryByPeriod(int $userAccountId, Carbon $startDate, Carbon $endDate): \Illuminate\Support\Collection
    {
        // Gunakan nama tabel dari config bila ada, default ke nama tabel standar
        $transactionsTable = config('db_tables.transaction', 'transactions');
        $accountsTable = config('db_tables.financial_account', 'financial_accounts');

        // Tentukan fungsi format tanggal berdasarkan driver database
        try {
            $driver = DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        } catch (\Exception $e) {
            $driver = 'mysql';
        }

        if ($driver === 'sqlite') {
            $periodeExpr = "strftime('%Y-%m', t.created_at)";
        } elseif ($driver === 'pgsql' || $driver === 'postgres') {
            $periodeExpr = "to_char(t.created_at, 'YYYY-MM')";
        } else {
            $periodeExpr = "DATE_FORMAT(t.created_at, '%Y-%m')"; // MySQL/MariaDB
        }

        // Susun DML SQL murni (alias tabel: t, fa)
        $sql = "
            SELECT 
                {$periodeExpr} AS periode,
                COALESCE(SUM(t.amount), 0) AS total_income
            FROM {$transactionsTable} t
            INNER JOIN {$accountsTable} fa ON t.financial_account_id = fa.id
            WHERE 
                t.user_account_id = ?
                AND fa.type = 'IN'
                AND t.balance_effect = 'increase'
                AND fa.is_group = 0
                AND t.created_at BETWEEN ? AND ?
            GROUP BY {$periodeExpr}
            ORDER BY periode ASC
        ";

        // Eksekusi raw SQL dengan parameter binding
        $rows = DB::select($sql, [
            $userAccountId,
            $startDate->toDateTimeString(),
            $endDate->toDateTimeString(),
        ]);

        return collect($rows);
    }

    // --- METODE BARU: MENGHAPUS SATU TRANSAKSI ---

    /**
     * Menghapus satu transaksi berdasarkan ID.
     *
     * @param int $transactionId ID dari transaksi yang akan dihapus.
     * @return bool True jika transaksi ditemukan dan berhasil dihapus, false sebaliknya.
     */
    public static function deleteTransactionById(int $transactionId): bool
    {
        // Temukan transaksi berdasarkan ID, dan hapus jika ditemukan.
        // Metode delete() akan mengembalikan true jika baris terhapus, false jika gagal.
        // Metode find() akan mengembalikan null jika tidak ditemukan.
        
        $transaction = static::find($transactionId);

        if ($transaction) {
            // Hapus transaksi. Mengembalikan boolean
            return $transaction->delete();
        }

        // Jika transaksi tidak ditemukan
        return false; 
    }
}