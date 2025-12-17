<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\FinancialAccountColumns; // Constant yang Anda berikan
use Illuminate\Support\Facades\DB;


class FinancialAccount extends Model
{
    use HasFactory; // Diletakkan di awal body class

    protected $table = 'financial_accounts';

    // Disusun Ulang: Daftar Fillable disesuaikan dan diperluas
    // (Mengambil referensi dari FinancialAccountColumns yang biasa diisi)
    protected $fillable = [
        FinancialAccountColumns::PARENT_ID,
        FinancialAccountColumns::NAME,
        FinancialAccountColumns::TYPE,
        FinancialAccountColumns::BALANCE,
        FinancialAccountColumns::IS_ACTIVE,
        // Tambahan kolom yang biasanya fillable (berdasarkan Constant yang umum)
        FinancialAccountColumns::INITIAL_BALANCE,
        FinancialAccountColumns::IS_GROUP,
        FinancialAccountColumns::DESCRIPTION,
        FinancialAccountColumns::SORT_ORDER,
        FinancialAccountColumns::LEVEL,
    ];

    protected $casts = [
        // Menggunakan Constant untuk 'is_active' dan menambahkan casts yang umum (misal: balance)
        FinancialAccountColumns::BALANCE => 'integer',
        FinancialAccountColumns::INITIAL_BALANCE => 'integer',
        FinancialAccountColumns::IS_GROUP => 'boolean',
        FinancialAccountColumns::IS_ACTIVE => 'boolean',
    ];

    /**
     * Relasi ke Parent Account
     */
    public function parent()
    {
        return $this->belongsTo(self::class, FinancialAccountColumns::PARENT_ID);
    }

    /**
     * Scope Eloquent untuk memfilter akun yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where(FinancialAccountColumns::IS_ACTIVE, true);
    }

    /**
     * TUGAS: Mengambil FinancialAccounts yang is_active = true menggunakan raw SQL.
     * Mengganti getActiveAccounts() yang ada dengan prepared statement.
     */
    public static function getActiveAccounts()
    {
        // Mendapatkan nama tabel dari instance Model (mempertimbangkan config/construct)
        $modelInstance = new self();
        $tableName = $modelInstance->getTable();
        
        // Menggunakan prepared statement untuk keamanan (PRACTICE TERBAIK)
        $sql = "SELECT * FROM {$tableName} WHERE is_active = ?";
        
        // true akan dikonversi menjadi 1 oleh DB
        return DB::select($sql, [true]); 
    }

    /**
     * Konstruktor: Digunakan untuk mengambil nama tabel dari konfigurasi.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.financial_account', 'financial_accounts');
    }

    /**
     * Method kustom untuk mendapatkan Financial Account berdasarkan ID menggunakan raw SQL.
     */
    public function getById($id){
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $result = DB::select($sql, [$id]);
        return !empty($result) ? $result[0] : null;
    }
}