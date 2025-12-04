<?php

namespace App\Models;

use App\Constants\UserAccountColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class UserAccount extends Model
{
    use HasFactory;

    protected $table = 'user_accounts';

    /**
     * Table tidak menggunakan timestamps (created_at & updated_at)
     */
    public $timestamps = false;

    /**
     * Fillable attributes menggunakan konfigurasi terpusat
     * dari UserAccountColumns
     */
    public function getFillable()
    {
        return UserAccountColumns::getFillable();
    }

    /**
     * Hidden attributes
     */
    protected $hidden = [
        UserAccountColumns::PASSWORD,
    ];

    /**
     * Casts
     */
    protected $casts = [
        UserAccountColumns::IS_ACTIVE   => 'boolean',
        UserAccountColumns::VERIFIED_AT => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, UserAccountColumns::ID_USER);
    }

    /**
     * Hapus satu UserAccount menggunakan raw query
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
