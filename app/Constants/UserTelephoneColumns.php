<?php

namespace App\Constants;

class UserTelephoneColumns
{
    public const ID         = 'id';
    public const USER_ID    = 'user_id';
    public const NUMBER     = 'number';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public static function getFillable(): array
    {
        return [
            self::USER_ID,
            self::NUMBER,
        ];
    }
}
