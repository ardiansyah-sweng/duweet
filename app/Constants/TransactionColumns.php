<?php

namespace App\Constants;

class TransactionColumns
{
    //  Kunci utama
    public const ID = 'id';

    // Relasi antar tabel
    public const TRANSACTION_GROUP_ID = 'transaction_group_id';
    public const USER_ACCOUNT_ID = 'user_account_id';
    public const FINANCIAL_ACCOUNT_ID = 'financial_account_id';

    // Informasi transaksi
    public const ENTRY_TYPE = 'entry_type';
    public const AMOUNT = 'amount';
    public const BALANCE_EFFECT = 'balance_effect';
    public const DESCRIPTION = 'description';
    public const IS_BALANCE = 'is_balance';

    // Kolom waktu
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * Mengembalikan daftar kolom yang dapat diisi (fillable)
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
