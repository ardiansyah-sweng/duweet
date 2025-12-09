<?php

namespace App\Constants;

class UserTelephoneColumns
{
    // Kolom USER_TELEPHONES (dari main)
    public const ID           = 'id';         // Primary Key (user_telephones)
    public const USER_ID      = 'user_id';    // Foreign key ke users.id
    public const NUMBER       = 'number';     // Nomor telepon (nullable)

    // Kolom dari HEAD (lokal)
    public const CREATED_AT   = 'created_at';
    public const UPDATED_AT   = 'updated_at';


    public static function getFillable(): array
    {
        return [
            self::USER_ID,
            self::NUMBER,
        ];
    }

    // Method dari main
    public static function getAllColumns(): array
    {
        return array_merge([self::ID], self::getFillable());
    }
}