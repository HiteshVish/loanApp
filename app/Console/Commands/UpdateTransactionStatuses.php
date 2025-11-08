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
        // Process both to ensure late fees are recalculated correctly
        $transactions = Transaction::whereIn('status', ['pending', 'delayed'])
            ->where('due_date', '<', Carbon::today(config('app.timezone')))
            ->with('loanDetail')
            ->orderBy('due_date', 'asc') // Process in chronological order for accurate consecutive counting
            ->get();

        $updatedCount = 0;

        foreach ($transactions as $transaction) {
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
                    
                    // Save status first to ensure database is updated
                    $transaction->save();
                    
                    // Refresh from database to get latest status of previous transactions
                    $transaction->refresh();
                    
                    // Get consecutive missed days before this transaction
                    $consecutiveMissed = $transaction->getConsecutiveMissedDays();
                    
                    // Only apply late fee after 3 consecutive missed payments
                    // consecutiveMissed = 3 means this is the 4th consecutive missed payment
                    // (3 previous + current = 4 total)
                    if ($consecutiveMissed >= 3) {
                        // Add 1 to include the current transaction in the count
                        $totalConsecutiveMissed = $consecutiveMissed + 1;
                        $lateFee = $transaction->calculateLateFee($transaction->loanDetail->loan_amount, $totalConsecutiveMissed);
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

