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

        // Get all pending and delayed transactions with past due dates
        $transactions = Transaction::whereIn('status', ['pending', 'delayed'])
            ->where('due_date', '<', Carbon::today(config('app.timezone')))
            ->where('status', '!=', 'completed')
            ->with('loanDetail')
            ->orderBy('due_date', 'asc')
            ->get();

        if ($transactions->isEmpty()) {
            $this->info('No transactions to update.');
            return Command::SUCCESS;
        }

        $today = Carbon::today(config('app.timezone'));
        $loanAmount = $transactions->first()->loanDetail->loan_amount ?? 0;
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

        // Second pass: Re-fetch delayed transactions to get fresh data
        $delayedTransactions = Transaction::where('loan_id', $transactions->first()->loan_id)
            ->where('status', 'delayed')
            ->where('due_date', '<', $today)
            ->with('loanDetail')
            ->orderBy('due_date', 'asc')
            ->get();

        // Calculate late fees for all delayed transactions
        foreach ($delayedTransactions as $transaction) {
            // Get consecutive missed count BEFORE this transaction
            $consecutiveMissed = $transaction->getConsecutiveMissedDays();
            $totalConsecutiveMissed = $consecutiveMissed + 1; // Include current transaction
            
            // Only apply late fee if we have 4+ consecutive missed payments
            if ($totalConsecutiveMissed >= 4) {
                // Calculate late fee based on position in sequence
                // Day 4 (1st day past grace): 0.5% × 1
                // Day 5 (2nd day past grace): 0.5% × 2
                $daysPastGrace = $totalConsecutiveMissed - 3;
                
                // Late fee = 0.5% per day for each day past the grace period
                $lateFee = $loanAmount * 0.005 * $daysPastGrace;
                $transaction->late_fee = round($lateFee, 2);
            } else {
                // Paid within 3-day grace period, no late fee
                $transaction->late_fee = 0;
            }
            
            $transaction->save();
        }

        $this->info("Updated {$updatedCount} transaction(s) from pending to delayed.");
        
        return Command::SUCCESS;
    }
}

