<?php

namespace App\Enums;

/**
 * Enum untuk tipe entri Transaksi (Debit/Kredit)
 */
enum TransactionEntryType: string
{
    case DEBIT  = 'debit';
    case CREDIT = 'credit';
}
