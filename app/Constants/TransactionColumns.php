<?php

namespace App\Constants;

class TransactionColumns
{
    // Kunci utama
    public const ID = 'id'; // Primary Key - identitas unik

    // Relasi antar tabel
    public const TRANSACTION_GROUP_ID = 'transaction_group_id'; // ID grup transaksi (UUID unik)
    public const USER_ACCOUNT_ID = 'user_account_id'; // Foreign Key ke tabel user_accounts.id
    public const FINANCIAL_ACCOUNT_ID = 'financial_account_id'; // Foreign Key ke tabel financial_accounts.id

    // Informasi transaksi
    public const ENTRY_TYPE = 'entry_type'; // Jenis transaksi: 'debit' atau 'kredit'
    public const AMOUNT = 'amount'; // Jumlah nominal transaksi (bilangan besar, selalu positif)
    public const BALANCE_EFFECT = 'balance_effect'; // Efek terhadap saldo: 'increase' atau 'decrease'
    public const DESCRIPTION = 'description'; // Deskripsi transaksi
    public const IS_BALANCE = 'is_balance'; // Status grup transaksi penyeimbang (boolean)

    // Kolom waktu
    public const CREATED_AT = 'created_at'; // Waktu pembuatan data
    public const UPDATED_AT = 'updated_at'; // Waktu pembaruan data

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
