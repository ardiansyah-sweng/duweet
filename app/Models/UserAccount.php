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
     * This table does not use created_at/updated_at timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Casting otomatis.
     */
    protected $casts = [
        UserAccountColumns::IS_ACTIVE => 'boolean',
        UserAccountColumns::VERIFIED_AT => 'datetime',
    ];

    /**
     * Hidden fields (password tidak ditampilkan).
     */
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_account_id');
    }

    /**
     * Relasi ke UserFinancialAccounts
     * Setiap UserAccount bisa memiliki beberapa akun keuangan
     */
    public function userFinancialAccounts()
    {
        return $this->hasMany(UserFinancialAccount::class, 'user_id', 'user_id');
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
            DB::delete(
                "DELETE FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?",
                [$id]
            );

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
