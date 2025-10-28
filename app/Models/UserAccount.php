<?php

namespace App\Models;

use App\Constants\UserAccountColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class UserAccount extends Model
{
    protected $table = 'user_accounts';

    protected $fillable = [
        UserAccountColumns::ID_USER,
        UserAccountColumns::USERNAME,
        UserAccountColumns::EMAIL,
        UserAccountColumns::PASSWORD,
        UserAccountColumns::VERIFIED_AT,
        UserAccountColumns::IS_ACTIVE,
    ];

    protected $casts = [
        UserAccountColumns::IS_ACTIVE => 'boolean',
        UserAccountColumns::VERIFIED_AT => 'datetime',
    ];

    protected $hidden = [
        UserAccountColumns::PASSWORD,
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, UserAccountColumns::ID_USER);
    }

    /**
     * Hapus satu UserAccount berdasarkan ID dengan raw query
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
