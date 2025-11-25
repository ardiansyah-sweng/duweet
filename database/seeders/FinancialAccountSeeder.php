<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class FinancialAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $table = config('db_tables.financial_account', 'financial_accounts');

        // Disable foreign key checks for truncate
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        DB::table($table)->truncate();

        // Re-enable
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $types = ['AS', 'IN', 'EX', 'SP', 'LI'];

        for ($i = 1; $i <= 10; $i++) {
            FinancialAccount::create([
                FinancialAccountColumns::PARENT_ID => null,
                FinancialAccountColumns::NAME => $faker->unique()->company . ' ' . $faker->randomElement(['Account','Wallet','Fund','Branch']),
                FinancialAccountColumns::TYPE => $faker->randomElement($types),
                FinancialAccountColumns::BALANCE => $faker->numberBetween(0, 10000000),
                FinancialAccountColumns::INITIAL_BALANCE => $faker->numberBetween(0, 10000000),
                FinancialAccountColumns::IS_GROUP => false,
                FinancialAccountColumns::DESCRIPTION => $faker->sentence(),
                FinancialAccountColumns::IS_ACTIVE => $faker->boolean(80),
                FinancialAccountColumns::SORT_ORDER => $i,
                FinancialAccountColumns::LEVEL => 0,
            ]);
        }

        $this->command->info('FinancialAccount seeder completed: 10 fake accounts created.');
    }
}
