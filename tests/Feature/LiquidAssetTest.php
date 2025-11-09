<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FinancialAccount;
use App\Models\UserFinancialAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiquidAssetTest extends TestCase
{
    use RefreshDatabase;

    public function test_total_liquid_asset_only_counts_active_leaf_asset_accounts(): void
    {
        $user = User::factory()->create();

        // Asset leaf
        $fa1 = FinancialAccount::create([
            'name' => 'Cash', 'type' => 'AS', 'balance' => 1000, 'initial_balance' => 1000,
            'is_group' => false, 'description' => null, 'is_active' => true,
        ]);
        UserFinancialAccount::create([
            'user_id' => $user->id, 'financial_account_id' => $fa1->id,
            'balance' => 1000, 'initial_balance' => 1000, 'is_active' => true,
        ]);

        // Inactive asset (should be excluded)
        $fa2 = FinancialAccount::create([
            'name' => 'Old Wallet', 'type' => 'AS', 'balance' => 500, 'initial_balance' => 500,
            'is_group' => false, 'description' => null, 'is_active' => true,
        ]);
        UserFinancialAccount::create([
            'user_id' => $user->id, 'financial_account_id' => $fa2->id,
            'balance' => 500, 'initial_balance' => 500, 'is_active' => false,
        ]);

        // Liability (not counted)
        $fa3 = FinancialAccount::create([
            'name' => 'Debt', 'type' => 'LI', 'balance' => 700, 'initial_balance' => 700,
            'is_group' => false, 'description' => null, 'is_active' => true,
        ]);
        UserFinancialAccount::create([
            'user_id' => $user->id, 'financial_account_id' => $fa3->id,
            'balance' => 700, 'initial_balance' => 700, 'is_active' => true,
        ]);

        $this->assertEquals(1000, $user->fresh()->totalLiquidAsset());
    }
}
