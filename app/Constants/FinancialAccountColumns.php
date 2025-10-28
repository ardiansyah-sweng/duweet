<?php

namespace App\Constants;

class FinancialAccountColumns
{
    public const ID              = 'id';
    public const PARENT_ID       = 'parent_id';
    public const NAME            = 'name';
    public const TYPE            = 'type';
    public const BALANCE         = 'balance';
    public const INITIAL_BALANCE = 'initial_balance';
    public const IS_GROUP        = 'is_group';
    public const DESCRIPTION     = 'description';
    public const IS_ACTIVE       = 'is_active';
    public const COLOR           = 'color';
    public const ICON            = 'icon';
    public const SORT_ORDER      = 'sort_order';
    public const LEVEL           = 'level';
    public const CREATED_AT      = 'created_at';
    public const UPDATED_AT      = 'updated_at';

    public static function getFillable(): array
    {
        return [
            self::PARENT_ID,
            self::NAME,
            self::TYPE,
            self::BALANCE,
            self::INITIAL_BALANCE,
            self::IS_GROUP,
            self::DESCRIPTION,
            self::IS_ACTIVE,
            self::COLOR,
            self::ICON,
            self::SORT_ORDER,
            self::LEVEL,
        ];
    }
}
