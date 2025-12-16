<?php

namespace App\Constants;

class UserFinancialAccountColumns
{
    public const TABLE = 'user_financial_accounts';
    public const ID = 'id';
    public const USER_ACCOUNT_ID = 'user_account_id';
    public const FINANCIAL_ACCOUNT_ID = 'financial_account_id';
    public const INITIAL_BALANCE = 'initial_balance';
    public const BALANCE = 'balance';
    public const IS_ACTIVE = 'is_active';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * Kolom yang bisa diisi (fillable)
     */
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

    /**
     * Semua kolom (termasuk ID)
     */
    public static function getAllColumns(): array
    {
        return array_merge([self::ID], self::getFillable());
    }
}