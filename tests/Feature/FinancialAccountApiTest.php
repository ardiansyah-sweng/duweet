<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\FinancialAccount;
use App\Constants\FinancialAccountColumns as AccountColumns;

class FinancialAccountApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_only_active_by_default()
    {
        FinancialAccount::factory()->create([AccountColumns::IS_ACTIVE => true]);
        FinancialAccount::factory()->create([AccountColumns::IS_ACTIVE => false]);

        $resp = $this->getJson('/api/financial-account');
        $resp->assertStatus(200);
        $data = $resp->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals(1, $data[0][AccountColumns::IS_ACTIVE]);
    }

    public function test_index_respects_is_active_query_param()
    {
        FinancialAccount::factory()->create([AccountColumns::IS_ACTIVE => true]);
        FinancialAccount::factory()->create([AccountColumns::IS_ACTIVE => false]);

        $resp = $this->getJson('/api/financial-account?is_active=false');
        $resp->assertStatus(200);
        $data = $resp->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals(0, $data[0][AccountColumns::IS_ACTIVE]);
    }
}
