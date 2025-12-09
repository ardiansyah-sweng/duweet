<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns;

class FinancialAccountSeeder extends Seeder
{
    public function run(): void
    {
        FinancialAccount::create([
            FinancialAccountColumns::PARENT_ID       => null,
            FinancialAccountColumns::NAME            => 'Bank BNI',
            FinancialAccountColumns::TYPE            => 'AS',
            FinancialAccountColumns::BALANCE         => 100000,
            FinancialAccountColumns::INITIAL_BALANCE => 100000,
            FinancialAccountColumns::DESCRIPTION     => 'Rekening Bank BNI',
            FinancialAccountColumns::IS_GROUP        => false,
            FinancialAccountColumns::IS_ACTIVE       => true,
            FinancialAccountColumns::SORT_ORDER      => 1,
            FinancialAccountColumns::LEVEL           => 0,
        ]);
            FinancialAccount::create([
            FinancialAccountColumns::PARENT_ID       => null,
            FinancialAccountColumns::NAME            => 'Bank BRI',
            FinancialAccountColumns::TYPE            => 'AS',
            FinancialAccountColumns::BALANCE         => 1500000,
            FinancialAccountColumns::INITIAL_BALANCE => 1500000,
            FinancialAccountColumns::DESCRIPTION     => 'Rekening Bank BRI',
            FinancialAccountColumns::IS_GROUP        => false,
            FinancialAccountColumns::IS_ACTIVE       => true,
            FinancialAccountColumns::SORT_ORDER      => 1,
            FinancialAccountColumns::LEVEL           => 0,
        ]);

        FinancialAccount::create([
            FinancialAccountColumns::PARENT_ID       => null,
            FinancialAccountColumns::NAME            => 'Bank BCA',
            FinancialAccountColumns::TYPE            => 'AS',
            FinancialAccountColumns::BALANCE         => 5000000,
            FinancialAccountColumns::INITIAL_BALANCE => 5000000,
            FinancialAccountColumns::DESCRIPTION     => 'Rekening utama BCA',
            FinancialAccountColumns::IS_GROUP        => false,
            FinancialAccountColumns::IS_ACTIVE       => false,
            FinancialAccountColumns::SORT_ORDER      => 2,
            FinancialAccountColumns::LEVEL           => 0,
        ]);
    }
}
