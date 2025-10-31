<?php

namespace App\Constants;

class UserFinancialAccountColumns
{
    public const ID                   = 'id';
    public const USER_ID              = 'user_id';
    public const FINANCIAL_ACCOUNT_ID = 'financial_account_id';
    public const BALANCE              = 'balance';
    public const INITIAL_BALANCE      = 'initial_balance';
    public const IS_ACTIVE            = 'is_active';
    public const CREATED_AT           = 'created_at';
    public const UPDATED_AT           = 'updated_at';

    public static function getFillable(): array
    {
        return [
            self::USER_ID,
            self::FINANCIAL_ACCOUNT_ID,
            self::BALANCE,
            self::INITIAL_BALANCE,
            self::IS_ACTIVE,
        ];
    }
}