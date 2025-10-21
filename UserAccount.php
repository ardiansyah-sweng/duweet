<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class UserAccount extends Model
{
    protected $table = 'user_accounts';

    protected $fillable = [
        'user_id',
        'account_id',
        'permission',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Account
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Akun::class);
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
            $deleteQuery = "DELETE FROM user_accounts WHERE id = ?";
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
