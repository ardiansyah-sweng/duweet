<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class UserAccountCountTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations in the in-memory sqlite testing database
        $this->artisan('migrate');
    }
    public function test_counts_per_user_returned()
    {
        // Clean tables (tests should run in isolated DB)
        DB::table('user_financial_accounts')->delete();
        DB::table('user_accounts')->delete();
        DB::table('users')->delete();

        // create users with required fields
        $u1 = DB::table('users')->insertGetId([
            'name' => 'User One',
            'email' => 'u1@example.com',
            'provinsi' => 'P',
            'kabupaten' => 'K',
            'kecamatan' => 'C',
            'jalan' => 'Jalan',
            'kode_pos' => '12345',
            'tanggal_lahir' => 1,
            'bulan_lahir' => 1,
            'tahun_lahir' => 2000,
            'usia' => 25,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $u2 = DB::table('users')->insertGetId([
            'name' => 'User Two',
            'email' => 'u2@example.com',
            'provinsi' => 'P',
            'kabupaten' => 'K',
            'kecamatan' => 'C',
            'jalan' => 'Jalan',
            'kode_pos' => '12345',
            'tanggal_lahir' => 1,
            'bulan_lahir' => 1,
            'tahun_lahir' => 2000,
            'usia' => 25,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // create user_accounts
        $ua1 = DB::table('user_accounts')->insertGetId([
            'id_user' => $u1,
            'username' => 'userone',
            'email' => 'u1@example.com',
            'password' => 'x',
            'verified_at' => null,
            'is_active' => 1,
        ]);

        $ua2 = DB::table('user_accounts')->insertGetId([
            'id_user' => $u2,
            'username' => 'usertwo',
            'email' => 'u2@example.com',
            'password' => 'x',
            'verified_at' => null,
            'is_active' => 1,
        ]);

        // create minimal financial_accounts rows referenced by user_financial_accounts
        DB::table('financial_accounts')->insert([
            [
                'name' => 'FA 1',
                'type' => 'AS',
                'balance' => 0,
                'initial_balance' => 0,
                'is_group' => 0,
                'description' => null,
                'is_active' => 1,
                'is_liquid' => 1,
                'sort_order' => 0,
                'level' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'FA 2',
                'type' => 'AS',
                'balance' => 0,
                'initial_balance' => 0,
                'is_group' => 0,
                'description' => null,
                'is_active' => 1,
                'is_liquid' => 1,
                'sort_order' => 0,
                'level' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'FA 3',
                'type' => 'AS',
                'balance' => 0,
                'initial_balance' => 0,
                'is_group' => 0,
                'description' => null,
                'is_active' => 1,
                'is_liquid' => 1,
                'sort_order' => 0,
                'level' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // create user_financial_accounts
        DB::table('user_financial_accounts')->insert([
            'user_account_id' => $ua1,
            'financial_account_id' => 1,
            'initial_balance' => 0,
            'balance' => 0,
            'is_active' => 1,
        ]);

        DB::table('user_financial_accounts')->insert([
            'user_account_id' => $ua1,
            'financial_account_id' => 2,
            'initial_balance' => 0,
            'balance' => 0,
            'is_active' => 1,
        ]);

        DB::table('user_financial_accounts')->insert([
            'user_account_id' => $ua2,
            'financial_account_id' => 3,
            'initial_balance' => 0,
            'balance' => 0,
            'is_active' => 1,
        ]);

        $response = $this->getJson('/api/users/accounts/count');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertIsArray($data);

        $foundU1 = collect($data)->firstWhere('user_id', $u1);
        $foundU2 = collect($data)->firstWhere('user_id', $u2);

        $this->assertEquals(2, (int) ($foundU1['total_accounts'] ?? 0));
        $this->assertEquals(1, (int) ($foundU2['total_accounts'] ?? 0));
    }
}
