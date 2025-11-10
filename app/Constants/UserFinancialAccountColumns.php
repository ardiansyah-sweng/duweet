<?php

namespace App\Constants;

class UserFinancialAccountColumns
{
    public const TABLE = 'user_financial_accounts';

    // Primary & Foreign Keys sesuai PRD
    public const ID = 'id';
    public const ID_USER = 'user_id';
    public const ID_FINANCIAL_ACCOUNT = 'financial_account_id';

    // Kolom keuangan sesuai PRD
    public const BALANCE = 'balance';
    public const INITIAL_BALANCE = 'initial_balance';

    // Status
    public const IS_ACTIVE = 'is_active';

    public static function getFillable(): array
    {
        return [
            self::ID_USER,
            self::ID_FINANCIAL_ACCOUNT,
            self::BALANCE,
            self::INITIAL_BALANCE,
            self::IS_ACTIVE,
        ];
    }

    public static function getAllColumns(): array
    {
        return array_merge([self::ID], self::getFillable());
    }

    public static function getPrimaryKey(): string
    {
        return self::ID;
    }

    public static function getForeignKeys(): array
    {
        return [
            self::ID_USER,
            self::ID_FINANCIAL_ACCOUNT,
        ];
    }
}