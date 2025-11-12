<?php

namespace App\Constants;

class UserTelephoneColumns
{
    // Kolom USER_TELEPHONES
    public const ID        = 'id';           // Primary Key (user_telephones)
    public const USER_ID   = 'user_id';      // Foreign key ke users.id
    public const NUMBER    = 'number';       // Nomor telepon (nullable)

    public static function getFillable(): array
    {
        return [
            self::USER_ID,
            self::NUMBER,
        ];
    }

    public static function getAllColumns(): array
    {
        return array_merge([self::ID], self::getFillable());
    }
}
