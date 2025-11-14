<?php

namespace Database\Seeders;

use App\Models\Cashout;
use App\Models\UserAccount;
use App\Constants\CashoutColumns;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CashoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all user accounts
        $userAccounts = UserAccount::all();

        if ($userAccounts->isEmpty()) {
            $this->command->warn('No user accounts found. Please seed UserAccount first.');
            return;
        }

        // Array of payment methods
        $paymentMethods = [
            CashoutColumns::METHOD_BANK_TRANSFER,
            CashoutColumns::METHOD_CASH,
            CashoutColumns::METHOD_E_WALLET,
        ];

        // Array of statuses
        $statuses = [
            CashoutColumns::STATUS_PENDING,
            CashoutColumns::STATUS_APPROVED,
            CashoutColumns::STATUS_REJECTED,
            CashoutColumns::STATUS_COMPLETED,
        ];

        // Create sample cashout records for the last 12 months
        $baseDateMonthsAgo = now()->subMonths(12);

        // Create 5 cashout records per user account distributed across 12 months
        foreach ($userAccounts as $userAccount) {
            for ($i = 0; $i < 5; $i++) {
                // Random date within last 12 months
                $daysOffset = rand(0, 365);
                $requestDate = $baseDateMonthsAgo->copy()->addDays($daysOffset);

                // Random amount between 100,000 - 10,000,000 (in smallest currency unit)
                $amount = rand(100000, 10000000);

                // Random payment method
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

                // 70% chance of completed, 15% approved, 10% pending, 5% rejected
                $statusRandom = rand(1, 100);
                if ($statusRandom <= 70) {
                    $status = CashoutColumns::STATUS_COMPLETED;
                } elseif ($statusRandom <= 85) {
                    $status = CashoutColumns::STATUS_APPROVED;
                } elseif ($statusRandom <= 95) {
                    $status = CashoutColumns::STATUS_PENDING;
                } else {
                    $status = CashoutColumns::STATUS_REJECTED;
                }

                // Prepare cashout data
                $cashoutData = [
                    'user_account_id' => $userAccount->id,
                    'amount' => $amount,
                    'status' => $status,
                    'request_date' => $requestDate,
                    'description' => "Cashout request #" . ($i + 1) . " for " . $userAccount->username,
                    'payment_method' => $paymentMethod,
                    'bank_account' => $paymentMethod === CashoutColumns::METHOD_BANK_TRANSFER 
                        ? '10' . rand(1000000000, 9999999999) 
                        : null,
                ];

                // If status is approved or completed, set approval date
                if (in_array($status, [CashoutColumns::STATUS_APPROVED, CashoutColumns::STATUS_COMPLETED])) {
                    $approvalDate = $requestDate->copy()->addDays(rand(1, 3));
                    $cashoutData['approval_date'] = $approvalDate;
                    $cashoutData['approved_by'] = 1; // Assume admin user has ID 1
                }

                // If status is completed, set completion date
                if ($status === CashoutColumns::STATUS_COMPLETED) {
                    $completionDate = $requestDate->copy()->addDays(rand(4, 10));
                    $cashoutData['completion_date'] = $completionDate;
                }

                // If status is rejected, add notes
                if ($status === CashoutColumns::STATUS_REJECTED) {
                    $rejectionReasons = [
                        'Insufficient balance',
                        'Invalid bank account',
                        'User verification required',
                        'Duplicate request',
                        'System error',
                    ];
                    $cashoutData['notes'] = $rejectionReasons[array_rand($rejectionReasons)];
                }

                Cashout::create($cashoutData);
            }
        }

        $totalCreated = Cashout::count();
        $this->command->info("✓ Created {$totalCreated} cashout records successfully!");

        // Display summary
        $summary = Cashout::statsPerStatus();
        $this->command->newLine();
        $this->command->info('Cashout Summary:');
        foreach ($summary as $status => $stats) {
            $this->command->info(
                "  {$status}: Total={$stats['total']}, Count={$stats['count']}, Average={$stats['average']}"
            );
        }
    }
}
