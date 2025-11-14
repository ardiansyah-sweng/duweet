<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use App\Constants\UserAccountColumns;

class UserAccount extends Model
{
    use HasFactory;

    protected $table = 'user_accounts';

    /**
     * Table ini tidak memakai timestamps.
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
     * Mengambil fillable dari UserAccountColumns
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

    /* ============================================================
     |  DML QUERY (RAW SQL)
     | ============================================================
     */

    /**
     * SELECT: Ambil semua UserAccount milik satu user tertentu (RAW SQL)
     * 
     * @param int $id_user
     * @return array
     */
    public static function getAccountsByUser($id_user)
    {
        return DB::select(
            "SELECT id, username, email, verified_at, is_active 
             FROM user_accounts 
             WHERE id_user = ?",
            [$id_user]
        );
    }

    /**
     * DELETE: Hapus satu UserAccount berdasarkan ID (RAW SQL)
     * 
     * @param int $id
     * @return array
     */
    public static function deleteUserAccountRaw($id)
    {
        try {
            $deleteQuery = "DELETE FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
            DB::delete($deleteQuery, [$id]);

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
}
