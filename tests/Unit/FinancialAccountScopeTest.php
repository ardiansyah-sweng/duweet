<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns as AccountColumns;

class FinancialAccountScopeTest extends TestCase
{
    use RefreshDatabase;
    public function test_scope_active_returns_only_active_accounts()
    {
        // Ensure there are an active and an inactive record
        FinancialAccount::factory()->create([AccountColumns::IS_ACTIVE => true]);
        FinancialAccount::factory()->create([AccountColumns::IS_ACTIVE => false]);

        $active = FinancialAccount::active()->get();

        $this->assertCount(1, $active);
        $this->assertTrue((bool) $active->first()->is_active);
    }
}
