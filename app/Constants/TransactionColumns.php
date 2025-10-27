<?php

namespace App\Constants;

class TransactionColumns
{
    public const ID             = 'id';
    public const ACCOUNT_ID     = 'account_id';
    public const USER_ID        = 'user_id';
    public const AMOUNT         = 'amount';
    public const DESCRIPTION    = 'description';
    public const CREATED_AT     = 'created_at';
    public const UPDATED_AT     = 'updated_at';

    public static function getFillable(): array
    {
        return [
            self::ACCOUNT_ID,
            self::USER_ID,
            self::AMOUNT,
            self::DESCRIPTION,
        ];
    }
}
