<?php

namespace App\Models;

use App\Constants\UserAccountColumns;
use App\Models\User;
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
        UserAccountColumns::LAST_LOGIN_AT => 'datetime',
    ];

    protected $hidden = [
        UserAccountColumns::PASSWORD,
    ];

    public function getFillable(): array
    {
        return UserAccountColumns::getFillable();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, UserAccountColumns::ID_USER);
    }

    /**
     * Ambil user yang tidak login dalam periode tertentu
     */
    public static function query_user_yang_tidak_login_dalam_periode_tertentu($tanggalMulai, $tanggalSelesai)
    {
        $sql = "
            SELECT *
            FROM user_accounts
            WHERE last_login_at IS NULL
               OR last_login_at NOT BETWEEN ? AND ?
        ";

        return DB::select($sql, [$tanggalMulai, $tanggalSelesai]);
    }
}