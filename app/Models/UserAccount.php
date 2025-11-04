<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\UserAccountColumns;
use Illuminate\Support\Facades\DB;

class UserAccount extends Model
{
    protected $table = 'user_accounts';
    protected $primaryKey = UserAccountColumns::ID;

    // Cannot call a method to initialize a property at compile time in PHP.
    // Initialize fillable in the constructor instead.
    protected $fillable = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = UserAccountColumns::getFillable();
    }

    /**
     * Query murni mencari user berdasarkan email.
     */
    public static function cariUserByEmail(string $email)
    {
        return DB::table('user_accounts')
                 ->where(UserAccountColumns::EMAIL, $email)
                 ->first();
    }

    /**
     * Reset password user berdasarkan email.
     */
    public static function resetPasswordByEmail(string $email, string $newPassword)
    {
        return DB::table('user_accounts')
                 ->where(UserAccountColumns::EMAIL, $email)
                 ->update([
                     UserAccountColumns::PASSWORD => bcrypt($newPassword),
                 ]);
    }
}
