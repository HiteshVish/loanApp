<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Carbon\Carbon;

class UpdateTransactionStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update transaction statuses from pending to delayed for overdue payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting transaction status update...');

        $today = Carbon::today(config('app.timezone'));
        $todayString = $today->toDateString(); // Convert to Y-m-d format for database comparison
        
        // Get all pending and delayed transactions with past due dates
        $transactions = Transaction::whereIn('status', ['pending', 'delayed'])
            ->where('due_date', '<', $todayString)
            ->with('loanDetail')
            ->orderBy('loan_id', 'asc')
            ->orderBy('due_date', 'asc')
            ->get();

        if ($transactions->isEmpty()) {
            $this->info('No transactions to update.');
            return Command::SUCCESS;
        }

        $updatedCount = 0;

        // First pass: Update status and days_late for all transactions
        foreach ($transactions as $transaction) {
            $dueDate = Carbon::parse($transaction->due_date)->setTimezone(config('app.timezone'))->startOfDay();
            
            if ($dueDate->lt($today)) {
                // Calculate days late correctly (today - due_date)
                $daysLate = $today->diffInDays($dueDate, false);
                
                if ($daysLate > 0) {
                    $transaction->days_late = $daysLate;
                    $transaction->status = 'delayed';
                    $transaction->save();
                    $updatedCount++;
                }
            }
        }

        // Second pass: Re-fetch ALL delayed transactions after status updates
        $allDelayedTransactions = Transaction::where('status', 'delayed')
            ->where('due_date', '<', $todayString)
            ->with('loanDetail')
            ->orderBy('loan_id', 'asc')
            ->orderBy('due_date', 'asc')
            ->get();

        if ($allDelayedTransactions->isEmpty()) {
            $this->info('No delayed transactions found after first pass.');
            return Command::SUCCESS;
        }

        // Group delayed transactions by loan_id
        $transactionsByLoan = $allDelayedTransactions->groupBy('loan_id');
        
        foreach ($transactionsByLoan as $loanId => $loanTransactions) {
            $delayedTransactions = $loanTransactions;

            if ($delayedTransactions->isEmpty()) {
                continue;
            }

            $loanAmount = $delayedTransactions->first()->loanDetail->loan_amount ?? 0;

            // Check if we have 4+ consecutive missed payments (crossed the 3-day deadline)
            $maxConsecutiveMissed = 0;
            foreach ($delayedTransactions as $transaction) {
                $consecutiveMissed = $transaction->getConsecutiveMissedDays();
                $totalConsecutiveMissed = $consecutiveMissed + 1;
                $maxConsecutiveMissed = max($maxConsecutiveMissed, $totalConsecutiveMissed);
            }

            // Calculate late fees for all delayed transactions in this loan
            foreach ($delayedTransactions as $transaction) {
                $consecutiveMissed = $transaction->getConsecutiveMissedDays();
                $totalConsecutiveMissed = $consecutiveMissed + 1;
                
                // If we have 4+ consecutive missed payments, apply late fee to ALL delayed transactions
                if ($maxConsecutiveMissed >= 4) {
                    // Flat 0.5% per transaction (not cumulative)
                    $lateFee = $loanAmount * 0.005; // Flat 0.5% per transaction
                    $transaction->late_fee = round($lateFee, 2);
                } else {
                    // Paid within 3-day grace period, no late fee
                    $transaction->late_fee = 0;
                }
                
                $transaction->save();
            }
        }

        $this->info("Updated {$updatedCount} transaction(s) from pending to delayed.");
        
        return Command::SUCCESS;
    }
}

