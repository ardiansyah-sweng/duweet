<?php

namespace App\Constants;

class TransactionColumns
{
    public const ID                     = 'id';
    public const TRANSACTION_GROUP_ID   = 'transaction_group_id';
    public const USER_ID                = 'user_id';
    public const ACCOUNT_ID             = 'account_id';
    public const ENTRY_TYPE             = 'entry_type';
    public const AMOUNT                 = 'amount';
    public const BALANCE_EFFECT         = 'balance_effect';
    public const DESCRIPTION            = 'description';
    public const IS_BALANCE             = 'is_balance';
    public const CREATED_AT             = 'created_at';
    public const UPDATED_AT             = 'updated_at';

    public static function getFillable(): array
    {
        return [
            self::TRANSACTION_GROUP_ID,
            self::USER_ID,
            self::ACCOUNT_ID,
            self::ENTRY_TYPE,
            self::AMOUNT,
            self::BALANCE_EFFECT,
            self::DESCRIPTION,
            self::IS_BALANCE,
        ];
    }
}
