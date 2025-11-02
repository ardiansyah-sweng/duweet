<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Constants\FinancialAccountColumns as Col;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $table = 'financial_accounts';

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table($table)->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $accounts = [
            [
                Col::NAME => 'Aset',
                Col::TYPE => 'AS',
                Col::IS_GROUP => true,
                'children' => [
                    [Col::NAME => 'Kas', Col::TYPE => 'AS', Col::IS_GROUP => false],
                    [Col::NAME => 'Bank', Col::TYPE => 'AS', Col::IS_GROUP => false],
                ],
            ],
            [
                Col::NAME => 'Kewajiban',
                Col::TYPE => 'LI',
                Col::IS_GROUP => true,
                'children' => [
                    [Col::NAME => 'Hutang Usaha', Col::TYPE => 'LI', Col::IS_GROUP => false],
                ],
            ],
        ];

        foreach ($accounts as $account) {
            $this->insertAccount($account);
        }
    }

    private function insertAccount(array $accountData, ?int $parentId = null): void
    {
        $id = DB::table('financial_accounts')->insertGetId([
            Col::PARENT_ID        => $parentId,
            Col::NAME             => $accountData[Col::NAME],
            Col::TYPE             => $accountData[Col::TYPE],
            Col::BALANCE          => $accountData[Col::INITIAL_BALANCE] ?? 0,
            Col::INITIAL_BALANCE  => $accountData[Col::INITIAL_BALANCE] ?? 0,
            Col::IS_GROUP         => $accountData[Col::IS_GROUP] ?? false,
            Col::DESCRIPTION      => $accountData[Col::DESCRIPTION] ?? null,
            Col::IS_ACTIVE        => $accountData[Col::IS_ACTIVE] ?? true,
            Col::SORT_ORDER       => $accountData[Col::SORT_ORDER] ?? 0,
            Col::LEVEL            => $accountData[Col::LEVEL] ?? ($parentId ? 2 : 1),
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        if (isset($accountData['children'])) {
            foreach ($accountData['children'] as $child) {
                $this->insertAccount($child, $id);
            }
        }
    }
}
