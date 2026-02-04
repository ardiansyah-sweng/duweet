<?php

namespace App\Models;

use App\Constants\UserAccountColumns;
use App\Constants\UserColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

    public function getKeyName()
    {
        return UserAccountColumns::getPrimaryKey();
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

    // --- RAW SQL METHODS ---

    public static function insertUserAccountRaw(array $data)
    {
        $password = Hash::make($data[UserAccountColumns::PASSWORD]);
        $verifiedAt = now(); 
        $isActive = 1;

        $query = "INSERT INTO user_accounts (
                    " . UserAccountColumns::ID_USER . ", 
                    " . UserAccountColumns::USERNAME . ", 
                    " . UserAccountColumns::EMAIL . ", 
                    " . UserAccountColumns::PASSWORD . ", 
                    " . UserAccountColumns::VERIFIED_AT . ", 
                    " . UserAccountColumns::IS_ACTIVE . "
                  ) VALUES (?, ?, ?, ?, ?, ?)";
        
        return DB::insert($query, [
            $data[UserAccountColumns::ID_USER], 
            $data[UserAccountColumns::USERNAME], 
            $data[UserAccountColumns::EMAIL], 
            $password, 
            $verifiedAt, 
            $isActive
        ]);
    }

    public static function updateUserAccountRaw($id, array $data)
    {
        $updateSets = [];
        $bindings = [];

        if (array_key_exists(UserAccountColumns::USERNAME, $data)) {
            $updateSets[] = UserAccountColumns::USERNAME . " = ?";
            $bindings[] = $data[UserAccountColumns::USERNAME];
        }
        if (array_key_exists(UserAccountColumns::EMAIL, $data)) {
            $updateSets[] = UserAccountColumns::EMAIL . " = ?";
            $bindings[] = $data[UserAccountColumns::EMAIL];
        }
        if (array_key_exists(UserAccountColumns::PASSWORD, $data)) {
            $updateSets[] = UserAccountColumns::PASSWORD . " = ?";
            $bindings[] = Hash::make($data[UserAccountColumns::PASSWORD]);
        }
        if (array_key_exists(UserAccountColumns::IS_ACTIVE, $data)) {
            $updateSets[] = UserAccountColumns::IS_ACTIVE . " = ?";
            $bindings[] = $data[UserAccountColumns::IS_ACTIVE];
        }

        if (empty($updateSets)) return 0;

        $bindings[] = $id;
        $query = "UPDATE user_accounts SET " . implode(', ', $updateSets) . 
                 " WHERE " . UserAccountColumns::ID . " = ?";

        return DB::update($query, $bindings);
    }

    public static function deleteUserAccountRaw($id)
    {
        try {
            DB::delete("DELETE FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?", [$id]);
            return ['success' => true, 'message' => 'UserAccount berhasil dihapus'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public static function cariUserById($id)
    {
        $result = DB::select("SELECT * FROM user_accounts WHERE " . UserAccountColumns::ID . " = ? LIMIT 1", [$id]);
        return $result[0] ?? null;
    }

    public static function cariUserByEmail($email)
    {
        $result = DB::select("SELECT * FROM user_accounts WHERE email = ? LIMIT 1", [$email]);
        return $result[0] ?? null;
    }

    public static function resetPasswordByEmail($email, $newPassword)
    {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        return DB::update("UPDATE user_accounts SET password = ? WHERE email = ?", [$hashed, $email]);
    }

    public static function cariUserByEmailLogin(string $email, string $password)
    {
        $user = DB::select("SELECT * FROM user_accounts WHERE email = ? LIMIT 1", [$email]);
        if (!empty($user) && Hash::check($password, $user[0]->password)) {
            return $user[0];
        }
        return null;
    }

    public static function cariUserByUsernameLogin(string $username, string $password)
    {
        $user = DB::select("SELECT * FROM user_accounts WHERE username = ? LIMIT 1", [$username]);
        if (!empty($user) && Hash::check($password, $user[0]->password)) {
            return $user[0];
        }
        return null;
    }

    public static function HitungTotalAccountperUser($userId)
    {
        $query = "SELECT u.id AS user_id, u.name, u.email, COUNT(ua.id) AS total_accounts
            FROM users u LEFT JOIN user_accounts ua ON ua.user_id = u.id
            WHERE u.id = ? GROUP BY u.id, u.name, u.email LIMIT 1";

        $result = DB::selectOne($query, [$userId]);
        return $result ? (array) $result : null;
    }
}