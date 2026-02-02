<?php

namespace App\Models;

use App\Constants\UserAccountColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        UserAccountColumns::IS_ACTIVE    => 'boolean',
        UserAccountColumns::VERIFIED_AT  => 'datetime',
    ];

    protected $hidden = [
        UserAccountColumns::PASSWORD,
    ];

    /**
     * Fillable attributes defined in constant class
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
     * Update password user yang sedang login (diambil dari HEAD)
     */
    public static function updatePassword($newPassword)
    {
        $userId = Auth::id();
        if (!$userId) {
            return false;
        }

        $hashed = bcrypt($newPassword);

        return DB::update("
            UPDATE user_accounts
            SET password = ?, updated_at = NOW()
            WHERE user_id = ?
        ", [$hashed, $userId]);
    }
public static function updatePasswordById($id, $newPassword)
{
    $hashed = bcrypt($newPassword);

    return DB::update("
        UPDATE user_accounts
        SET password = ?
        WHERE id = ?
    ", [$hashed, $id]);
}

    /**
     * Raw delete user account
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
