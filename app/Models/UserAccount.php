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
     * Query users yang belum setup account
     * Static method untuk mendapatkan users yang belum punya user_accounts record
     */
    public static function usersWithoutAccount()
    {
        return User::whereDoesntHave('userAccounts')
            ->orderBy('created_at', 'desc');
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
     * DML: Cari user account by ID menggunakan RAW QUERY
     */
    public static function cariUserById($id)
    {
        $query = "SELECT * FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
        $result = DB::select($query, [$id]);

        return $result[0] ?? null;
    }

    /**
     * DML: Cari user by email menggunakan RAW QUERY
     */
    public static function cariUserByEmail($email)
    {
        $query = "SELECT * FROM user_accounts WHERE email = ? LIMIT 1";
        $result = DB::select($query, [$email]);

        return $result[0] ?? null;
    }

    /**
     * DML: Reset password by email (RAW UPDATE)
     */
    public static function resetPasswordByEmail($email, $newPassword)
    {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);

        $query = "
            UPDATE user_accounts 
            SET password = ?
            WHERE email = ?
        ";

        return DB::update($query, [$hashed, $email]);
    }
}
