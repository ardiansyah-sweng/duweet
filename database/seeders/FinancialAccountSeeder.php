<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns;

class FinancialAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Account 1: Aktif
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

        // Account 2: Tidak Aktif
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
