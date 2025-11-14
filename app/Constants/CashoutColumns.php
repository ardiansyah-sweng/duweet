<?php

namespace App\Constants;

class CashoutColumns
{
    // Kunci utama
    public const ID = 'id'; // Primary Key - identitas unik

    // Relasi antar tabel
    public const USER_ACCOUNT_ID = 'user_account_id'; // Foreign Key ke tabel user_accounts.id

    // Informasi cashout
    public const AMOUNT = 'amount'; // Jumlah nominal cashout (bilangan besar)
    public const STATUS = 'status'; // Status cashout: 'pending', 'approved', 'rejected', 'completed'
    public const REQUEST_DATE = 'request_date'; // Tanggal request cashout
    public const APPROVAL_DATE = 'approval_date'; // Tanggal persetujuan cashout
    public const COMPLETION_DATE = 'completion_date'; // Tanggal penyelesaian cashout
    public const DESCRIPTION = 'description'; // Deskripsi/alasan cashout
    public const NOTES = 'notes'; // Catatan tambahan
    public const PAYMENT_METHOD = 'payment_method'; // Metode pembayaran: 'bank_transfer', 'cash', 'e_wallet'
    public const BANK_ACCOUNT = 'bank_account'; // Nomor rekening bank (opsional)
    public const APPROVED_BY = 'approved_by'; // User ID yang approve (admin)

    // Kolom waktu
    public const CREATED_AT = 'created_at'; // Waktu pembuatan data
    public const UPDATED_AT = 'updated_at'; // Waktu pembaruan data

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';

    // Payment method constants
    public const METHOD_BANK_TRANSFER = 'bank_transfer';
    public const METHOD_CASH = 'cash';
    public const METHOD_E_WALLET = 'e_wallet';

    /**
     * Mengembalikan daftar kolom yang dapat diisi (fillable)
     */
    public static function getFillable(): array
    {
        return [
            self::USER_ACCOUNT_ID,
            self::AMOUNT,
            self::STATUS,
            self::REQUEST_DATE,
            self::APPROVAL_DATE,
            self::COMPLETION_DATE,
            self::DESCRIPTION,
            self::NOTES,
            self::PAYMENT_METHOD,
            self::BANK_ACCOUNT,
            self::APPROVED_BY,
        ];
    }

    /**
     * Mengembalikan daftar status yang valid
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_COMPLETED,
        ];
    }

    /**
     * Mengembalikan daftar metode pembayaran yang valid
     */
    public static function getValidPaymentMethods(): array
    {
        return [
            self::METHOD_BANK_TRANSFER,
            self::METHOD_CASH,
            self::METHOD_E_WALLET,
        ];
    }
}
