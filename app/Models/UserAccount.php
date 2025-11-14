<?php

namespace App\Models;

use App\Constants\UserAccountColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB; // Pastikan DB di-import

class UserAccount extends Model
{
    use HasFactory;

    protected $table = 'user_accounts';

    /**
     * This table does not use created_at/updated_at timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $casts = [
        UserAccountColumns::IS_ACTIVE => 'boolean',
        UserAccountColumns::VERIFIED_AT => 'datetime',
    ];

    protected $hidden = [
        UserAccountColumns::PASSWORD,
    ];

    /**
     * Get the fillable attributes for the model.
     * Uses centralized definition from UserAccountColumns constant class.
     *
     * @return array<string>
     */
    public function getFillable()
    {
        return UserAccountColumns::getFillable();
    }

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, UserAccountColumns::ID_USER);
    }

    /**
     * Hapus satu UserAccount berdasarkan ID dengan raw query
     * * @param int $id
     * @return array
     */
    public static function deleteUserAccountRaw($id)
    {
        try {
            // Menggunakan konstanta untuk nama kolom ID
            $deleteQuery = "DELETE FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
            
            $deletedRows = DB::delete($deleteQuery, [$id]);

            if ($deletedRows === 0) {
                 return [
                    'success' => false,
                    'message' => 'UserAccount tidak ditemukan'
                ];
            }

            return [
                'success' => true,
                'message' => 'UserAccount berhasil dihapus'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal menghapus UserAccount: ' . $e->getMessage()
            ];
        }
    }

    // --- TAMBAHAN BARU DI SINI ---

    /**
     * Update user account using raw SQL query.
     *
     * @param int $id
     * @param array $data Data yang sudah divalidasi dan siap update
     * @return array
     */
    public static function updateUserAccountRaw($id, array $data)
    {
        // 1. Cek dulu apakah user dengan ID tersebut ada
        $existsQuery = "SELECT 1 FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
        $exists = DB::select($existsQuery, [$id]);
        
        if (empty($exists)) {
            return ['success' => false, 'message' => 'UserAccount tidak ditemukan'];
        }

        // 2. Jika tidak ada data untuk diupdate, kembalikan sukses (opsional)
        if (empty($data)) {
            $selectQuery = "SELECT * FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
            $userAccount = DB::selectOne($selectQuery, [$id]);
            return ['success' => true, 'message' => 'Tidak ada data untuk diupdate', 'data' => $userAccount];
        }

        // 3. Bangun query UPDATE secara dinamis
        $setClauses = [];
        $bindings = [];

        foreach ($data as $column => $value) {
            // Pastikan kolom valid (meskipun sudah divalidasi di controller)
            if (in_array($column, UserAccountColumns::getFillable())) {
                $setClauses[] = "`$column` = ?";
                $bindings[] = $value;
            }
        }

        // Jika setelah filtering tidak ada kolom valid
        if (empty($setClauses)) {
             $selectQuery = "SELECT * FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
             $userAccount = DB::selectOne($selectQuery, [$id]);
             return ['success' => true, 'message' => 'Tidak ada data valid untuk diupdate', 'data' => $userAccount];
        }

        // Tambahkan ID untuk WHERE clause
        $bindings[] = $id; 
        $setString = implode(', ', $setClauses);

        $query = "UPDATE user_accounts SET $setString WHERE " . UserAccountColumns::ID . " = ?";

        try {
            // 4. Eksekusi query
            DB::update($query, $bindings);

            // 5. Ambil data terbaru setelah diupdate untuk dikembalikan
            $selectQuery = "SELECT * FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
            $updatedAccount = DB::selectOne($selectQuery, [$id]);

            return [
                'success' => true,
                'message' => 'UserAccount berhasil diupdate',
                'data' => $updatedAccount
            ];

        } catch (\Exception $e) {
            // 6. Tangani jika ada error database
            return [
                'success' => false,
                'message' => 'Gagal mengupdate UserAccount: ' . $e->getMessage()
            ];
        }
    }
}