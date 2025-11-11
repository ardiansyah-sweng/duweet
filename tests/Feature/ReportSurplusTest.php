<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ReportSurplusTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Seed minimal financial_accounts and transactions then assert surplus calculation.
     */
    public function test_surplus_by_period_calculation()
    {
        // Create user first
        $userId = DB::table('users')->insertGetId([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create user account
        $userAccountId = DB::table('user_accounts')->insertGetId([
            'id_user' => $userId,
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create financial accounts
        $inAccountId = DB::table('financial_accounts')->insertGetId([
            'name' => 'Salary',
            'type' => 'IN',
            'balance' => 0,
            'initial_balance' => 0,
            'is_group' => false,
            'is_active' => true,
            'sort_order' => 1,
            'level' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $exAccountId = DB::table('financial_accounts')->insertGetId([
            'name' => 'Food',
            'type' => 'EX',
            'balance' => 0,
            'initial_balance' => 0,
            'is_group' => false,
            'is_active' => true,
            'sort_order' => 1,
            'level' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert transactions: income (kredit) and expense (debit)
        DB::table('transactions')->insert([
            [
                'transaction_group_id' => 'grp-1',
                'user_account_id' => $userAccountId,
                'financial_account_id' => $inAccountId,
                'entry_type' => 'kredit',
                'amount' => 100000,
                'balance_effect' => 'increase',
                'description' => 'Salary Jan',
                'is_balance' => true,
                'created_at' => '2025-01-15 12:00:00',
                'updated_at' => '2025-01-15 12:00:00',
            ],
            [
                'transaction_group_id' => 'grp-2',
                'user_account_id' => $userAccountId,
                'financial_account_id' => $exAccountId,
                'entry_type' => 'debit',
                'amount' => 40000,
                'balance_effect' => 'decrease',
                'description' => 'Food Jan',
                'is_balance' => true,
                'created_at' => '2025-01-18 18:00:00',
                'updated_at' => '2025-01-18 18:00:00',
            ],
        ]);

        // Call debug route
        $response = $this->getJson('/admin/reports/surplus-debug?start=2025-01-01&end=2025-01-31&period=monthly');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertCount(1, $data);

        $row = $data[0];
        $this->assertEquals('2025-01', $row['period']);
        $this->assertEquals(100000, (int) $row['income']);
        $this->assertEquals(40000, (int) $row['expense']);
        $this->assertEquals(60000, (int) $row['surplus']);
    }
}
