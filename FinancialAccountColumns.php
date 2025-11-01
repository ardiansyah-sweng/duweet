<?php

namespace App\Constants;

class FinancialAccountColumns
{
    public const ID                = 'id';              // Primary Key
    public const PARENT_ID         = 'parent_id';       // Foreign key to accounts.id
    public const NAME              = 'name';            // Nama account (e.g., "Bank")
    public const TYPE              = 'type';            // Jenis account (IN/EX/SP/LI/AS)
    public const BALANCE           = 'balance';         // Saldo saat ini
    public const INITIAL_BALANCE   = 'initial_balance'; // Saldo awal
    public const IS_GROUP          = 'is_group';        // Boolean, apakah account ini group?
    public const DESCRIPTION       = 'description';     // Deskripsi account (nullable)
    public const IS_ACTIVE         = 'is_active';       // Status aktif/nonaktif
    public const COLOR             = 'color';           // Warna untuk UI (hex code, nullable)
    public const ICON              = 'icon';            // Icon untuk UI (nullable)
    public const SORT_ORDER        = 'sort_order';      // Urutan tampilan
    public const LEVEL             = 'level';           // Level kedalaman
    public const CREATED_AT        = 'created_at';
    public const UPDATED_AT        = 'updated_at';

    /**
     * Get fillable columns (exclude id, created_at, updated_at)
     *
     * @return array
     */
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