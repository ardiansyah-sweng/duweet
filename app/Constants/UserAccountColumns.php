<?php

namespace App\Constants;

class UserAccountColumns
{
    public const ID                = 'id';
    public const USER_ID           = 'user_id';
    public const USERNAME          = 'username';
    public const EMAIL             = 'email';
    public const PASSWORD          = 'password';
    public const EMAIL_VERIFIED_AT = 'email_verified_at';
    public const IS_ACTIVE         = 'is_active';
    public const CREATED_AT        = 'created_at';
    public const UPDATED_AT        = 'updated_at';
    public static function getFillable(): array
    {
        return [
            self::USER_ID,
            self::USERNAME,
            self::EMAIL,
            self::PASSWORD,
            self::IS_ACTIVE,
        ];
    }
}
