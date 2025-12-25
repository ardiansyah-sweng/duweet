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
     * Model ini tidak menggunakan created_at dan updated_at.
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
     * Fillable (menggunakan constant class).
     */
    public function getFillable()
    {
        return UserAccountColumns::getFillable();
    }

    /**
     * Relasi ke tabel users.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, UserAccountColumns::ID_USER);
    }

    /**
     * Relasi ke transaksi
     */
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
     * RAW DELETE USER ACCOUNT (DML)
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

    /**
     * DML: Cari user berdasarkan username
     */
    public static function findByUsername(string $username)
    {
        $result = DB::select(
            "SELECT * FROM user_accounts WHERE username = ? LIMIT 1",
            [$username]
        );

        return $result[0] ?? null;
    }

    /**
     * DML: Cari user berdasarkan email
     */
    public static function cariUserByEmail($email)
    {
        $result = DB::select(
            "SELECT * FROM user_accounts WHERE email = ? LIMIT 1",
            [$email]
        );

        return $result[0] ?? null;
    }

    /**
     * DML: Reset password berdasarkan email
     */
    public static function resetPasswordByEmail($email, $newPassword)
    {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);

        return DB::update(
            "UPDATE user_accounts SET password = ? WHERE email = ?",
            [$hashed, $email]
        );
    }
}
