<?php

namespace App\Constants;

class FinancialAccountColumns
{
    public const ID             = 'id'; // Primary Key
    public const PARENT_ID      = 'parent_id'; // Foreign Key to accounts.id
    public const NAME           = 'name';   // Account name (e.g., "Bank", "BCA Savings", "Wallet")
    public const TYPE           = 'type'; // Account type (e.g., "AS" for Asset, "LI" for Liability)
    public const IS_GROUP       = 'is_group'; // Boolean to indicate if the account is a group
    public const IS_ACTIVE      = 'is_active'; // Boolean to indicate if the account is active
    public const BALANCE        = 'balance'; // Current balance of the account
    public const INITIAL_BALANCE = 'initial_balance'; // Initial balance when the account was created
    public const DESCRIPTION    = 'description'; // Optional description for the account
    //public const PATH           = 'path'; // Hierarchical path (e.g., "1/2/5")
    public const SORT_ORDER     = 'sort_order'; // Sort order for the account
    public const LEVEL          = 'level'; // Level in the hierarchy (0 = root, 1 = child, 2 = grandchild)
    public const CREATED_AT     = 'created_at';
    public const UPDATED_AT     = 'updated_at';

    /**
     * Get fillable columns (exclude id, created_at, updated_at)
     */
    public static function getFillable(): array
    {
        return [
            self::NAME,
            self::PARENT_ID,
            self::TYPE,
            self::BALANCE,
            self::INITIAL_BALANCE,
            self::DESCRIPTION,
            self::IS_GROUP,
            self::IS_ACTIVE,
            self::SORT_ORDER,
            self::LEVEL,
        ];
    }
}
