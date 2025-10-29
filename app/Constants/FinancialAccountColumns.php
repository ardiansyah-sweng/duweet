<?php

namespace App\Constants;

class FinancialAccountColumns
{
    public const TABLE = 'financial_accounts';

    // Kolom utama
    public const ID         = 'id';
    public const CODE       = 'code';       
    public const NAME       = 'name';     
    public const TYPE       = 'type';     
    public const IS_ACTIVE  = 'is_active'; 
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const TYPE_ASSET      = 'AS';
    public const TYPE_LIABILITY  = 'LI';
    public const TYPE_EQUITY     = 'EQ';
    public const TYPE_REVENUE    = 'RV';
    public const TYPE_EXPENSE    = 'EX';
}
