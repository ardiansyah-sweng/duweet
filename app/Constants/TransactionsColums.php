<?php

namespace App\Constants;

class TransactionColumns
{
    // Primary Key
    public const ID = 'id'; // Kunci utama (identifier unik)

    // Relasi dan referensi
    public const TRANSACTION_GROUP_ID = 'transaction_group_id'; // ID grup transaksi (UUID unik)
    public const USER_ID = 'user_id'; // Kunci asing (Foreign Key) mengacu ke tabel users.id
    public const ACCOUNT_ID = 'account_id'; // Kunci asing (Foreign Key) mengacu ke tabel financial_accounts.id

    // Informasi transaksi
    public const ENTRY_TYPE = 'entry_type'; // Jenis transaksi: 'debit' atau 'kredit'
    public const AMOUNT = 'amount'; // Jumlah nominal transaksi (bilangan besar)
    public const BALANCE_EFFECT = 'balance_effect'; // Efek terhadap saldo: 'menambah' atau 'mengurangi'
    public const DESCRIPTION = 'description'; // Deskripsi transaksi
    public const IS_BALANCE = 'is_balance'; // Status apakah transaksi merupakan penyeimbang (true/false)

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
            self::USER_ID,
            self::ACCOUNT_ID,
            self::ENTRY_TYPE,
            self::AMOUNT,
            self::BALANCE_EFFECT,
            self::DESCRIPTION,
            self::IS_BALANCE,
        ];
    }
}
