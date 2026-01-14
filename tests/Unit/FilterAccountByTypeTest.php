<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\FinancialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FilterAccountByTypeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        FinancialAccount::create([
            'name' => 'Bank Utama',
            'type' => 'AS',
            'balance' => 1000000,
            'initial_balance' => 1000000,
            'is_active' => true,
            'is_group' => false,
        ]);

        FinancialAccount::create([
            'name' => 'Properti',
            'type' => 'AS',
            'balance' => 5000000,
            'initial_balance' => 5000000,
            'is_active' => true,
            'is_group' => false,
        ]);

        FinancialAccount::create([
            'name' => 'Gaji',
            'type' => 'IN',
            'balance' => 0,
            'initial_balance' => 0,
            'is_active' => true,
            'is_group' => true,
        ]);

        FinancialAccount::create([
            'name' => 'Bonus',
            'type' => 'IN',
            'balance' => 0,
            'initial_balance' => 0,
            'is_active' => true,
            'is_group' => false,
            'parent_id' => 3,
        ]);

        FinancialAccount::create([
            'name' => 'Makan',
            'type' => 'EX',
            'balance' => 500000,
            'initial_balance' => 500000,
            'is_active' => true,
            'is_group' => false,
        ]);

        FinancialAccount::create([
            'name' => 'Utang Kartu Kredit',
            'type' => 'LI',
            'balance' => 2000000,
            'initial_balance' => 2000000,
            'is_active' => true,
            'is_group' => false,
        ]);

        FinancialAccount::create([
            'name' => 'Belanja',
            'type' => 'SP',
            'balance' => 1000000,
            'initial_balance' => 1000000,
            'is_active' => true,
            'is_group' => false,
        ]);

        FinancialAccount::create([
            'name' => 'Akun Tidak Aktif',
            'type' => 'AS',
            'balance' => 0,
            'initial_balance' => 0,
            'is_active' => false,
            'is_group' => false,
        ]);

        // Create user
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        // Link accounts to user
        $user->financialAccounts()->attach([1, 2, 3, 4]);
    }

    public function test_filter_by_single_type()
    {
        $assetAccounts = FinancialAccount::ofType('AS')->get();
        $this->assertEquals(3, $assetAccounts->count()); // 2 active + 1 inactive
        $this->assertTrue($assetAccounts->every(fn($acc) => $acc->type === 'AS'));
    }

    public function test_filter_by_multiple_types()
    {
        $accounts = FinancialAccount::ofType(['IN', 'EX'])->get();
        $this->assertEquals(3, $accounts->count()); // 2 IN + 1 EX
        $this->assertTrue($accounts->every(fn($acc) => in_array($acc->type, ['IN', 'EX'])));
    }

    public function test_filter_by_comma_separated_string()
    {
        $accounts = FinancialAccount::ofType('SP,LI')->get();
        $this->assertEquals(2, $accounts->count());
        $this->assertTrue($accounts->every(fn($acc) => in_array($acc->type, ['SP', 'LI'])));
    }

    public function test_filter_active_accounts()
    {
        $activeAccounts = FinancialAccount::active()->get();
        $this->assertEquals(7, $activeAccounts->count());
        $this->assertTrue($activeAccounts->every(fn($acc) => $acc->is_active === true));
    }

    public function test_filter_groups_only()
    {
        $groups = FinancialAccount::groups()->get();
        $this->assertEquals(1, $groups->count());
        $this->assertTrue($groups->first()->is_group === true);
    }

    public function test_filter_active_by_type()
    {
        $activeAssets = FinancialAccount::activeByType('AS')->get();
        $this->assertEquals(2, $activeAssets->count());
        $this->assertTrue($activeAssets->every(fn($acc) => $acc->type === 'AS' && $acc->is_active === true));
    }

    public function test_summary_by_type()
    {
        $summary = FinancialAccount::summaryByType();
        $this->assertGreaterThan(0, $summary->count());
        
        $assets = $summary->where('type', 'AS')->first();
        $this->assertEquals(2, $assets->count); // 2 active ASET
        $this->assertEquals(6000000, $assets->total_balance); // 1M + 5M
    }

    public function test_grouped_by_type()
    {
        $grouped = FinancialAccount::groupedByType();
        $this->assertGreaterThan(0, $grouped->count());
        
        $assets = $grouped->where('type', 'AS')->first();
        $this->assertEquals(2, $assets->total);
    }

    public function test_user_get_accounts_by_type()
    {
        $user = User::first();
        $userAssets = $user->getAccountsByType('AS');
        $this->assertEquals(2, $userAssets->count());
    }

    public function test_user_get_active_accounts()
    {
        $user = User::first();
        $activeAccounts = $user->getActiveAccounts();
        $this->assertGreaterThan(0, $activeAccounts->count());
    }

    public function test_user_get_accounts_summary()
    {
        $user = User::first();
        $summary = $user->getAccountsSummary();
        $this->assertGreaterThan(0, $summary->count());
    }
}
