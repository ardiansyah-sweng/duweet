<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Auth extends Model
{
    public static function loginByUsername($username)
    {
        $query = "SELECT 
                    ua.id as user_account_id,
                    ua.id_user,
                    ua.username,
                    ua.email,
                    ua.password,
                    ua.is_active,
                    u.name as user_name
                  FROM user_accounts ua
                  INNER JOIN users u ON ua.id_user = u.id
                  WHERE ua.username = ?
                  LIMIT 1";

        return DB::selectOne($query, [$username]);
    }

}
