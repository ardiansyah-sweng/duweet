<?php

namespace App\Enums;

enum TransactionEntryType: string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';
}