<?php

namespace App\Enums;

enum AccountType: string
{
    case INCOME = 'IN';
    case EXPENSES = 'EX';
    case SPENDING = 'SP';
    case LIABILITY = 'LI';
    case ASSET = 'AS';

    public function label(): string
    {
        return match($this) {
            self::INCOME => 'Income',
            self::EXPENSES => 'Expenses',
            self::SPENDING => 'Spending',
            self::LIABILITY => 'Liability',
            self::ASSET => 'Asset',
        };
    }
}

// Usage example:
// $accountType = AccountType::INCOME;
// echo $accountType->label(); // Output: Income
// echo AccountType::ASSET->label(); // Output: Asset
// echo AccountType::SPENDING->value; // Output: SP
// echo AccountType::from('EX')->label(); // Output: Expenses
// echo AccountType::from('LI')->value; // Output: LI
// echo AccountType::from('INVALID'); // Throws ValueError
// echo AccountType::from('INVALID')->label(); // Throws ValueError
// echo AccountType::from('INVALID')->value; // Throws ValueError
// echo AccountType::from('IN')->label(); // Output: Income
// echo AccountType::from('IN')->value; // Output: IN
// echo AccountType::from('EX')->label(); // Output: Expenses
// echo AccountType::from('EX')->value; // Output: EX
// echo AccountType::from('SP')->label(); // Output: Spending
// echo AccountType::from('SP')->value; // Output: SP

// <?php
// use App\Enums\AccountType;

// // Di migration
// $table->enum('type', array_column(AccountType::cases(), 'value'));

// // Di model
// protected $casts = [
//     'type' => AccountType::class,
// ];

// // Di controller
// $account->type = AccountType::INCOME;