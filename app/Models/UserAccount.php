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
    public $timestamps = false;

    protected $casts = [
        UserAccountColumns::IS_ACTIVE => 'boolean',
        UserAccountColumns::VERIFIED_AT => 'datetime',
    ];

    protected $hidden = [
        UserAccountColumns::PASSWORD,
    ];

    public function getFillable()
    {
        return UserAccountColumns::getFillable();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, UserAccountColumns::ID_USER);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_account_id');
    }

    public function userFinancialAccounts()
    {
        return $this->hasMany(UserFinancialAccount::class, 'user_id', 'user_id');
    }

    public static function deleteUserAccountRaw($id)
    {
        try {
            DB::delete(
                "DELETE FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?",
                [$id]
            );

            return ['success' => true, 'message' => 'UserAccount berhasil dihapus'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public static function cariUserById($id)
    {
        $result = DB::select(
            "SELECT * FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?",
            [$id]
        );

        return $result[0] ?? null;
    }

    public static function findByUsername(string $username, string $password)
    {
        $result = DB::select(
            "SELECT * FROM user_accounts WHERE username = ? AND password = ? LIMIT 1",
            [$username, $password]
        );

        return $result[0] ?? null;
    }

    public static function cariUserByEmail(string $email, string $password)
    {
        $result = DB::select(
            "SELECT * FROM user_accounts WHERE email = ? AND password = ? LIMIT 1",
            [$email, $password]
        );

        return $result[0] ?? null;
    }

    public static function resetPasswordByEmail($email, $newPassword)
    {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);

        return DB::update(
            "UPDATE user_accounts SET password = ? WHERE email = ?",
            [$hashed, $email]
        );
    }
}
