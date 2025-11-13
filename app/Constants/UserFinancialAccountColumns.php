<?php

namespace App\Constants;

class UserFinancialAccountColumns
{
    // Kolom utama
    public const ID = 'id'; // Primary key

    // Relasi antar tabel
    public const ID_USER = 'id_user'; // Foreign key ke tabel users.id
    public const FINANCIAL_ACCOUNT_ID = 'financial_account_id'; // FK ke financial_accounts.id

    // Informasi saldo
    public const BALANCE = 'balance'; // Saldo berjalan
    public const INITIAL_BALANCE = 'initial_balance'; // Saldo awal
    public const IS_ACTIVE = 'is_active'; // Status aktif/tidak

    // Timestamp
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * Kolom yang bisa diisi (fillable)
     */
    public static function getFillable(): array
    {
        return [
            self::ID_USER,
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