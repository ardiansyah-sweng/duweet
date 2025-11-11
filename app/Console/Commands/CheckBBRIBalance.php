<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckBBRIBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bbri:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check BBRI balance and asset calculations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== BBRI Balance Check ===");

        // 1. Check Financial Account Balance
        $bbri = DB::table('financial_accounts')->where('id', 20)->first();
        $this->info("Financial Account BBRI (ID: 20):");
        $this->info("- Name: {$bbri->name}");
        $this->info("- Initial Balance: Rp " . number_format($bbri->initial_balance));
        $this->info("- Current Balance: Rp " . number_format($bbri->balance));
        $this->newLine();

        // 2. Check Assets Table
        $this->info("Assets Table BBRI:");
        $assets = DB::table('assets')->where('financial_account_id', 20)->get();
        $totalValue = 0;
        foreach($assets as $asset) {
            // Asset table stores: lots and price per share
            // Calculation: lots × 100 shares/lot × price per share
            $shares = $asset->buy_quantity * 100;
            $value = $shares * $asset->bought_price;
            $totalValue += $value;
            $this->info("- ID: {$asset->id} | Date: {$asset->acquisition_date} | {$asset->buy_quantity} lots ({$shares} shares) @ Rp " . number_format($asset->bought_price) . " = Rp " . number_format($value));
        }
        $this->info("Total Asset Value: Rp " . number_format($totalValue));
        $this->newLine();

        // 3. Check Transactions
        $this->info("Transactions for BBRI:");
        $transactions = DB::table('transactions')
            ->where('financial_account_id', 20)
            ->orderBy('created_at')
            ->get();
            
        $runningBalance = 0;
        foreach($transactions as $trans) {
            $effect = ($trans->balance_effect === 'increase') ? '+' : '-';
            $runningBalance += ($trans->balance_effect === 'increase') ? $trans->amount : -$trans->amount;
            $this->info("- {$trans->entry_type}: {$effect}Rp " . number_format($trans->amount) . " (Running: Rp " . number_format($runningBalance) . ")");
        }
        $this->info("Final Transaction Balance: Rp " . number_format($runningBalance));
        $this->newLine();

        $this->info("=== Calculation Summary ===");
        $this->info("Expected Balance: Rp " . number_format($totalValue) . " (from assets)");
        $this->info("Financial Account: Rp " . number_format($bbri->balance));
        $this->info("Transaction Sum: Rp " . number_format($runningBalance));

        if ($bbri->balance != $totalValue) {
            $this->warn("\n⚠️ MISMATCH DETECTED!");
            $this->warn("Difference: Rp " . number_format(abs($bbri->balance - $totalValue)));
            
            if (count($assets) > 1) {
                $this->warn("⚠️ Multiple asset entries found - this might be the issue!");
            }
        } else {
            $this->info("\n✅ All balances match!");
        }

        return 0;
    }
}
