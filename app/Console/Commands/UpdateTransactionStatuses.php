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

        // Get all pending transactions with past due dates
        $pendingTransactions = Transaction::where('status', 'pending')
            ->where('due_date', '<', Carbon::today(config('app.timezone')))
            ->with('loanDetail')
            ->get();

        $updatedCount = 0;

        foreach ($pendingTransactions as $transaction) {
            // Use application timezone for date comparisons
            $dueDate = Carbon::parse($transaction->due_date)->setTimezone(config('app.timezone'))->startOfDay();
            $today = Carbon::today(config('app.timezone'));
            
            // Only process if due date is actually in the past
            if ($dueDate->lt($today)) {
                // Calculate days late (positive number for past dates)
                $daysLate = $dueDate->diffInDays($today, false);
                
                if ($daysLate > 0) {
                    $transaction->days_late = $daysLate;
                    $transaction->status = 'delayed';
                    
                    // Get consecutive missed days before this transaction
                    $consecutiveMissed = $transaction->getConsecutiveMissedDays();
                    
                    // Only apply late fee after 3 consecutive missed payments
                    if ($consecutiveMissed > 3) {
                        $lateFee = $transaction->calculateLateFee($transaction->loanDetail->loan_amount, $consecutiveMissed);
                        $transaction->late_fee = $lateFee > 0 ? round($lateFee, 2) : 0;
                    } else {
                        $transaction->late_fee = 0;
                    }
                    
                    $transaction->save();
                    $updatedCount++;
                }
            }
        }

        $this->info("Updated {$updatedCount} transaction(s) from pending to delayed.");
        
        return Command::SUCCESS;
    }
}

