<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FinancialAccount;
use App\Models\User;

class TestFilterQuery extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:filter-query';

    /**
     * The console command description.
     */
    protected $description = 'Test filtering account berdasarkan type';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('');
        $this->line(str_repeat('=', 60));
        $this->line('TEST FILTERING ACCOUNT BERDASARKAN TYPE');
        $this->line(str_repeat('=', 60));
        $this->line('');

        // Test 1: Filter berdasarkan satu tipe (ASET)
        $this->info('[TEST 1] Filter berdasarkan tipe ASET:');
        $assetAccounts = FinancialAccount::ofType('AS')->get();
        $this->line('Total akun ASET: ' . $assetAccounts->count());
        foreach ($assetAccounts as $acc) {
            $this->line('  - ' . $acc->name . ' (' . $acc->type . ')');
        }
        $this->line('');

        // Test 2: Filter berdasarkan multiple tipe
        $this->info('[TEST 2] Filter berdasarkan tipe INCOME dan EXPENSES:');
        $incomeExpense = FinancialAccount::ofType(['IN', 'EX'])->get();
        $this->line('Total akun INCOME/EXPENSES: ' . $incomeExpense->count());
        foreach ($incomeExpense as $acc) {
            $this->line('  - ' . $acc->name . ' (' . $acc->type . ')');
        }
        $this->line('');

        // Test 3: Filter dengan string terpisah koma
        $this->info('[TEST 3] Filter dengan string "SP,LI":');
        $spendingLiability = FinancialAccount::ofType('SP,LI')->get();
        $this->line('Total akun SPENDING/LIABILITY: ' . $spendingLiability->count());
        foreach ($spendingLiability as $acc) {
            $this->line('  - ' . $acc->name . ' (' . $acc->type . ')');
        }
        $this->line('');

        // Test 4: Filter aktif berdasarkan tipe
        $this->info('[TEST 4] Filter akun ASET yang AKTIF:');
        $activeAssets = FinancialAccount::ofType('AS')->active()->get();
        $this->line('Total akun ASET aktif: ' . $activeAssets->count());
        foreach ($activeAssets as $acc) {
            $this->line('  - ' . $acc->name . ' (Active: ' . ($acc->is_active ? 'Ya' : 'Tidak') . ')');
        }
        $this->line('');

        // Test 5: Summary per tipe
        $this->info('[TEST 5] Summary akun per tipe:');
        $summary = FinancialAccount::summaryByType();
        $this->line('Jumlah tipe akun: ' . $summary->count());
        foreach ($summary as $item) {
            $this->line('  - Tipe: ' . $item->type . ' | Count: ' . $item->count . ' | Total Balance: ' . $item->total_balance);
        }
        $this->line('');

        // Test 6: Grouped by type
        $this->info('[TEST 6] Grouping akun per tipe:');
        $grouped = FinancialAccount::groupedByType();
        $this->line('Hasil grouping:');
        foreach ($grouped as $item) {
            $this->line('  - Tipe: ' . $item->type . ' | Total: ' . $item->total);
        }
        $this->line('');

        // Test 7: Hanya grup akun
        $this->info('[TEST 7] Filter hanya akun grup:');
        $groupAccounts = FinancialAccount::groups()->get();
        $this->line('Total grup akun: ' . $groupAccounts->count());
        foreach ($groupAccounts as $acc) {
            $this->line('  - ' . $acc->name . ' (Is Group: ' . ($acc->is_group ? 'Ya' : 'Tidak') . ')');
        }
        $this->line('');

        // Test 8: Semua akun
        $this->info('[TEST 8] Semua akun:');
        $allAccounts = FinancialAccount::all();
        $this->line('Total semua akun: ' . $allAccounts->count());
        $this->line('');

        // Test 9: User accounts
        $this->info('[TEST 9] Akun finansial per user:');
        $users = User::all();
        if ($users->count() > 0) {
            foreach ($users as $user) {
                $userAccounts = $user->financialAccounts()->get();
                $this->line('  User: ' . $user->name . ' | Total akun: ' . $userAccounts->count());

                if ($userAccounts->count() > 0) {
                    $userAssets = $user->getAccountsByType('AS');
                    $this->line('    - Akun ASET: ' . $userAssets->count());
                    
                    $userActive = $user->getActiveAccounts();
                    $this->line('    - Akun Aktif: ' . $userActive->count());
                }
            }
        } else {
            $this->line('  Tidak ada user dalam database');
        }
        $this->line('');

        // Test 10: Combined filter
        $this->info('[TEST 10] Filter kombinasi (Aktif + Tipe ASET):');
        $combined = FinancialAccount::activeByType('AS')->get();
        $this->line('Total: ' . $combined->count());
        foreach ($combined as $acc) {
            $this->line('  - ' . $acc->name);
        }
        $this->line('');

        $this->line(str_repeat('=', 60));
        $this->line('✓ QUERY TEST COMPLETED');
        $this->line(str_repeat('=', 60));
        $this->line('');
    }
}
