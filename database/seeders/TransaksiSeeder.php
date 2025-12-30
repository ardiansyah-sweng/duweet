<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class TransaksiSeeder extends Seeder
{
    /**
     * Seed transaksi (transactions) data.
     * Creates 20 sample transactions and updates account balances accordingly.
     */
    public function run(): void
    {
        // Clear existing transactions
        DB::table('transaksi')->truncate();

        echo "Seeding transactions...\n";

        // Get all leaf accounts (accounts that can have transactions)
        $leafAccounts = Account::where('is_group', false)->get();

        if ($leafAccounts->isEmpty()) {
            echo "No leaf accounts found. Please run AccountSeeder first.\n";
            return;
        }

        // Create 20 sample transactions
        $transactions = Transaksi::factory()->count(20)->create();

        echo "Created {$transactions->count()} transactions.\n";

        // Update account balances based on transactions
        echo "Updating account balances...\n";
        
        foreach ($leafAccounts as $account) {
            $totalDebit = Transaksi::where('account_id', $account->id)
                ->where('type', 'debit')
                ->sum('amount');
            
            $totalCredit = Transaksi::where('account_id', $account->id)
                ->where('type', 'credit')
                ->sum('amount');

            // Calculate balance: initial_balance + debit - credit
            // (Adjust this logic based on your accounting convention)
            $balance = $account->initial_balance + $totalDebit - $totalCredit;
            
            $account->balance = $balance;
            $account->save();
        }

        // Recompute parent account balances
        echo "Recomputing parent account balances...\n";
        $groupAccounts = Account::where('is_group', true)->orderBy('level', 'desc')->get();
        
        foreach ($groupAccounts as $group) {
            $childrenBalance = Account::where('parent_id', $group->id)->sum('balance');
            $group->balance = $childrenBalance;
            $group->save();
        }

        echo "Transaksi seeding completed successfully!\n";
        echo "Total transactions: " . Transaksi::count() . "\n";
    }
}
