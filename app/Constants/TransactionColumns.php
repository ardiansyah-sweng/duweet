<?php

namespace App\Constants;

class TransactionColumns
{
    // Primary Key
    public const ID = 'id';

    // Relasi antar tabel
    public const TRANSACTION_GROUP_ID = 'transaction_group_id';       // UUID grup transaksi
    public const USER_ACCOUNT_ID      = 'user_account_id';            // FK ke user_accounts.id
    public const FINANCIAL_ACCOUNT_ID = 'financial_account_id';       // FK ke financial_accounts.id

    // Informasi transaksi
    public const ENTRY_TYPE     = 'entry_type';      // debit / kredit
    public const AMOUNT         = 'amount';          // nominal transaksi
    public const BALANCE_EFFECT = 'balance_effect';  // increase / decrease
    public const DESCRIPTION    = 'description';     // deskripsi transaksi
    public const IS_BALANCE     = 'is_balance';      // penanda balancing (boolean)

    // Timestamp
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * Kolom yang dapat diisi (fillable)
     */
    public static function getFillable(): array
    {
        return [
            self::TRANSACTION_GROUP_ID,
            self::USER_ACCOUNT_ID,
            self::FINANCIAL_ACCOUNT_ID,
            self::ENTRY_TYPE,
            self::AMOUNT,
            self::BALANCE_EFFECT,
            self::DESCRIPTION,
            self::IS_BALANCE,
        ];
    }
}
