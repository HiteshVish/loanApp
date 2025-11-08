<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanDetail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display all loans with their amounts and payment calculations
     */
    public function index(Request $request)
    {
        // Only show approved loans in Payment Management (exclude completed)
        $query = LoanDetail::with(['user', 'transactions'])
            ->where('status', 'approved');

        // Auto-update transaction statuses and late fees
        $this->updateTransactionStatuses();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('loan_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('email', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('user.userDetail', function($detailQuery) use ($search) {
                      $detailQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Get statistics (only for approved loans)
        $totalLoans = LoanDetail::where('status', 'approved')->count();
        $totalAmount = LoanDetail::where('status', 'approved')->sum('loan_amount');
        $pendingLoans = 0; // Not applicable for payment management
        $approvedLoans = $totalLoans; // All loans in payment are approved
        $rejectedLoans = 0; // Not applicable for payment management
        $thisMonthLoans = LoanDetail::where('status', 'approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Get paginated loans
        $loans = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate payment details for each loan
        foreach ($loans as $loan) {
            // Calculate loan details
            $loan->processing_fee = $loan->calculateProcessingFee();
            $loan->in_hand_amount = $loan->calculateInHandAmount();
            $loan->total_amount_with_interest = $loan->calculateTotalAmount();
            $loan->daily_emi = $loan->calculateDailyEMI();
            $loan->late_fee_per_day = $loan->calculateLateFeePerDay();
            
            // Get transaction statistics
            $loan->total_transactions = $loan->transactions->count();
            $loan->completed_transactions = $loan->transactions->where('status', 'completed')->count();
            $loan->pending_transactions = $loan->transactions->where('status', 'pending')->count();
            $loan->delayed_transactions = $loan->transactions->where('status', 'delayed')->count();
            
            // Calculate totals
            $loan->total_paid = $loan->transactions->where('status', 'completed')->sum('amount');
            $loan->total_late_fees = $loan->transactions->sum('late_fee');
            $loan->remaining_amount = $loan->total_amount_with_interest - $loan->total_paid;
        }

        return view('admin.payment.index', compact(
            'loans',
            'totalLoans',
            'totalAmount',
            'pendingLoans',
            'approvedLoans',
            'rejectedLoans',
            'thisMonthLoans'
        ));
    }

    /**
     * Show individual loan payment details
     */
    public function show($loanId)
    {
        // Find loan by loan_id (loan_id is the primary key)
        $loan = LoanDetail::with(['user', 'transactions' => function($query) {
            $query->orderBy('due_date', 'asc');
        }])->findOrFail($loanId);

        // Calculate loan details
        $loan->processing_fee = $loan->calculateProcessingFee();
        $loan->in_hand_amount = $loan->calculateInHandAmount();
        $loan->total_amount_with_interest = $loan->calculateTotalAmount();
        $loan->daily_emi = $loan->calculateDailyEMI();
        $loan->late_fee_per_day = $loan->calculateLateFeePerDay();

        // Transaction statistics
        $loan->total_transactions = $loan->transactions->count();
        $loan->completed_transactions = $loan->transactions->where('status', 'completed')->count();
        $loan->pending_transactions = $loan->transactions->where('status', 'pending')->count();
        $loan->delayed_transactions = $loan->transactions->where('status', 'delayed')->count();

        // Get transaction statistics with calculations (use paid_amount instead of status)
        $loan->total_paid = $loan->transactions->sum(function($t) {
            return $t->paid_amount ?? 0;
        });
        $loan->total_late_fees_paid = $loan->transactions->where('status', 'completed')->sum('late_fee');
        $loan->remaining_amount = $loan->total_amount_with_interest - $loan->total_paid;

        return view('admin.payment.show', compact('loan'));
    }

    /**
     * Record payment for a transaction
     */
    public function recordPayment(Request $request, $loanId)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'paid_amount' => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $transaction = Transaction::findOrFail($validated['transaction_id']);

        // Verify this transaction belongs to the loan
        if ($transaction->loan_id !== $loanId) {
            return back()->with('error', 'Invalid transaction for this loan.');
        }

        // Check if already fully paid
        if ($transaction->status === 'completed' && $transaction->paid_amount >= ($transaction->amount + $transaction->late_fee)) {
            return back()->with('error', 'This transaction has already been fully paid.');
        }

        $paidAmount = $validated['paid_amount'];
        $expectedAmount = $transaction->amount + $transaction->late_fee;
        $existingPaidAmount = $transaction->paid_amount ?? 0;
        $totalPaidAmount = $existingPaidAmount + $paidAmount;
        
        // Build notes - preserve existing notes
        $notes = [];
        if ($transaction->notes) {
            $notes[] = $transaction->notes;
        }
        if ($validated['notes']) {
            $notes[] = $validated['notes'];
        }
        
        // Update transaction with payment details
        $transaction->paid_date = $validated['payment_date'] ?? Carbon::today(config('app.timezone'));
        
        // Handle payment scenarios
        if ($totalPaidAmount < $expectedAmount) {
            // PARTIAL PAYMENT - Keep as pending/delayed
            $remaining = $expectedAmount - $totalPaidAmount;
            $notes[] = "Payment: ₹" . number_format($paidAmount, 2) . " (Total paid: ₹" . number_format($totalPaidAmount, 2) . ", Remaining: ₹" . number_format($remaining, 2) . ")";
            $transaction->paid_amount = $totalPaidAmount;
            $transaction->status = $transaction->status === 'delayed' ? 'delayed' : 'pending';
            $message = "Partial payment of ₹" . number_format($paidAmount, 2) . " recorded. Total paid: ₹" . number_format($totalPaidAmount, 2) . ", Remaining: ₹" . number_format($remaining, 2);
        } elseif ($totalPaidAmount == $expectedAmount) {
            // FULL PAYMENT - Mark as completed
            $transaction->paid_amount = $expectedAmount;
            $transaction->status = 'completed';
            if ($existingPaidAmount > 0) {
                $notes[] = "Payment: ₹" . number_format($paidAmount, 2) . " (Total: ₹" . number_format($totalPaidAmount, 2) . " - Full payment completed)";
            }
            $message = "Full payment of ₹" . number_format($paidAmount, 2) . " recorded successfully!";
        } else {
            // OVERPAYMENT - Mark as completed and apply excess to next transaction
            $excess = $totalPaidAmount - $expectedAmount;
            // Set paid_amount to exactly the expected amount (not the total paid)
            $transaction->paid_amount = $expectedAmount;
            $transaction->status = 'completed';
            $notes[] = "Payment: ₹" . number_format($paidAmount, 2) . " (Total: ₹" . number_format($totalPaidAmount, 2) . ", Overpayment: ₹" . number_format($excess, 2) . " applied to next transaction)";
            $message = "Payment of ₹" . number_format($paidAmount, 2) . " recorded. Total paid: ₹" . number_format($totalPaidAmount, 2) . ". Excess ₹" . number_format($excess, 2) . " applied to next transaction.";
            
            // Apply excess to next pending transaction
            $this->applyExcessToNextTransaction($loanId, $transaction->id, $excess);
        }
        
        $transaction->notes = implode(' | ', $notes);
        $transaction->save();

        // Check if loan is completed (all transactions paid)
        $loan = LoanDetail::find($loanId);
        if ($loan) {
            $loan->markAsCompleted();
        }

        return back()->with('success', $message);
    }

    /**
     * Apply excess payment to next pending transaction
     */
    private function applyExcessToNextTransaction($loanId, $currentTransactionId, $excessAmount)
    {
        // Get the current transaction to find its due date
        $currentTransaction = Transaction::find($currentTransactionId);
        
        // Find next pending/delayed transaction after this one
        $nextTransaction = Transaction::where('loan_id', $loanId)
            ->where('id', '!=', $currentTransactionId)
            ->where('due_date', '>', $currentTransaction->due_date)
            ->whereIn('status', ['pending', 'delayed'])
            ->orderBy('due_date', 'asc')
            ->first();
        
        if ($nextTransaction) {
            $existingPaid = $nextTransaction->paid_amount ?? 0;
            $nextTransaction->paid_amount = $existingPaid + $excessAmount;
            
            // Check if this makes it fully paid
            $expectedAmount = $nextTransaction->amount + $nextTransaction->late_fee;
            if ($nextTransaction->paid_amount >= $expectedAmount) {
                $nextTransaction->status = 'completed';
                $nextTransaction->paid_date = Carbon::today(config('app.timezone'));
                
                // If there's still excess, apply to next transaction recursively
                $remainingExcess = $nextTransaction->paid_amount - $expectedAmount;
                if ($remainingExcess > 0) {
                    $nextTransaction->paid_amount = $expectedAmount; // Set to exact amount
                    $nextTransaction->save();
                    $this->applyExcessToNextTransaction($loanId, $nextTransaction->id, $remainingExcess);
                } else {
                    $nextTransaction->save();
                }
            } else {
                // Still partial, update notes
                $remaining = $expectedAmount - $nextTransaction->paid_amount;
                $notes = $nextTransaction->notes ? $nextTransaction->notes . ' | ' : '';
                $notes .= "Excess from previous payment: ₹" . number_format($excessAmount, 2) . " (Remaining: ₹" . number_format($remaining, 2) . ")";
                $nextTransaction->notes = $notes;
                $nextTransaction->save();
            }
        }
    }

    /**
     * Manually mark loan as completed (admin can override)
     */
    public function markAsCompleted($loanId)
    {
        $loan = LoanDetail::find($loanId);
        
        if (!$loan) {
            return back()->with('error', 'Loan not found.');
        }

        $loan->status = 'completed';
        $loan->save();

        return back()->with('success', 'Loan marked as completed successfully!');
    }

    /**
     * Auto-update transaction statuses and late fees for overdue payments
     */
    private function updateTransactionStatuses()
    {
        // Log that method is being called
        Log::info('=== updateTransactionStatuses METHOD CALLED ===');
        
        $today = Carbon::today(config('app.timezone'));
        $todayString = $today->toDateString(); // Convert to Y-m-d format for database comparison
        
        Log::info('Date calculation', [
            'today' => $today->toDateString(),
            'todayString' => $todayString,
            'timezone' => config('app.timezone')
        ]);
        
        // Get all pending and delayed transactions with past due dates
        // Process both to ensure late fees are recalculated correctly
        $transactions = Transaction::whereIn('status', ['pending', 'delayed'])
            ->where('due_date', '<', $todayString)
            ->with('loanDetail')
            ->orderBy('loan_id', 'asc')
            ->orderBy('due_date', 'asc') // Process in chronological order for accurate consecutive counting
            ->get();

        if ($transactions->isEmpty()) {
            Log::info('No transactions found to update', [
                'today' => $todayString,
                'count' => Transaction::whereIn('status', ['pending', 'delayed'])->count()
            ]);
            return;
        }

        Log::info('Found transactions to update', [
            'count' => $transactions->count(),
            'today' => $todayString
        ]);

        // First pass: Update status and days_late for all transactions
        $updatedCount = 0;
        foreach ($transactions as $transaction) {
            $dueDate = Carbon::parse($transaction->due_date)->setTimezone(config('app.timezone'))->startOfDay();
            
            Log::info('Processing transaction in first pass', [
                'transaction_id' => $transaction->id,
                'loan_id' => $transaction->loan_id,
                'due_date' => $transaction->due_date,
                'due_date_parsed' => $dueDate->toDateString(),
                'today' => $today->toDateString(),
                'is_lt_today' => $dueDate->lt($today),
                'current_status' => $transaction->status
            ]);
            
            if ($dueDate->lt($today)) {
                // Calculate days late correctly (today - due_date)
                // Use $dueDate->diffInDays($today) to get positive days when due_date is in the past
                $daysLate = $dueDate->diffInDays($today, false);
                
                Log::info('Transaction is past due', [
                    'transaction_id' => $transaction->id,
                    'days_late' => $daysLate
                ]);
                
                if ($daysLate > 0) {
                    $oldStatus = $transaction->status;
                    $transaction->days_late = $daysLate;
                    $transaction->status = 'delayed';
                    $saved = $transaction->save();
                    
                    // Refresh to verify the save
                    $transaction->refresh();
                    
                    Log::info('Attempted to save transaction', [
                        'transaction_id' => $transaction->id,
                        'old_status' => $oldStatus,
                        'new_status' => $transaction->status,
                        'days_late' => $transaction->days_late,
                        'saved' => $saved,
                        'fresh_status' => $transaction->status
                    ]);
                    
                    $updatedCount++;
                } else {
                    Log::warning('Days late is not > 0', [
                        'transaction_id' => $transaction->id,
                        'days_late' => $daysLate
                    ]);
                }
            } else {
                Log::info('Transaction is not past due', [
                    'transaction_id' => $transaction->id,
                    'due_date' => $dueDate->toDateString(),
                    'today' => $today->toDateString()
                ]);
            }
        }
        
        Log::info('First pass completed', ['updated_count' => $updatedCount]);

        // Second pass: Re-fetch ALL delayed transactions after status updates
        // This ensures we get all loans that have delayed transactions, not just the ones from the original query
        $allDelayedTransactions = Transaction::where('status', 'delayed')
            ->where('due_date', '<', $todayString)
            ->with('loanDetail')
            ->orderBy('loan_id', 'asc')
            ->orderBy('due_date', 'asc')
            ->get();

        if ($allDelayedTransactions->isEmpty()) {
            Log::info('No delayed transactions found after first pass');
            return;
        }

        Log::info('Found delayed transactions for late fee calculation', [
            'count' => $allDelayedTransactions->count()
        ]);

        // Group delayed transactions by loan_id
        $transactionsByLoan = $allDelayedTransactions->groupBy('loan_id');
        
        foreach ($transactionsByLoan as $loanId => $loanTransactions) {
            $delayedTransactions = $loanTransactions;

            if ($delayedTransactions->isEmpty()) {
                Log::info('No delayed transactions found for loan', ['loan_id' => $loanId]);
                continue;
            }

            $loanAmount = $delayedTransactions->first()->loanDetail->loan_amount ?? 0;
            
            Log::info('Processing delayed transactions for loan', [
                'loan_id' => $loanId,
                'loan_amount' => $loanAmount,
                'delayed_count' => $delayedTransactions->count()
            ]);

            // Check if we have 4+ consecutive missed payments (crossed the 3-day deadline)
            $maxConsecutiveMissed = 0;
            foreach ($delayedTransactions as $transaction) {
                $consecutiveMissed = $transaction->getConsecutiveMissedDays();
                $totalConsecutiveMissed = $consecutiveMissed + 1;
                $maxConsecutiveMissed = max($maxConsecutiveMissed, $totalConsecutiveMissed);
            }
            
            Log::info('Consecutive missed calculation', [
                'loan_id' => $loanId,
                'max_consecutive_missed' => $maxConsecutiveMissed
            ]);

            // Calculate late fees for all delayed transactions in this loan
            // Logic: If 4+ consecutive missed days, late fee applies to ALL delayed transactions
            // Each transaction stores flat 0.5% late fee
            foreach ($delayedTransactions as $transaction) {
                // Get consecutive missed count BEFORE this transaction
                $consecutiveMissed = $transaction->getConsecutiveMissedDays();
                $totalConsecutiveMissed = $consecutiveMissed + 1; // Include current transaction
                
                // If we have 4+ consecutive missed payments, apply late fee to ALL delayed transactions
                // Each transaction gets flat 0.5% late fee
                if ($maxConsecutiveMissed >= 4) {
                    // Flat 0.5% per transaction (not cumulative)
                    // Day 2 (1st missed): 0.5%
                    // Day 3 (2nd missed): 0.5%
                    // Day 4 (3rd missed): 0.5%
                    // Day 5 (4th missed): 0.5%
                    // When paying, sum all: 0.5% + 0.5% + 0.5% + 0.5% = 2.0%
                    $lateFee = $loanAmount * 0.005; // Flat 0.5% per transaction
                    $transaction->late_fee = round($lateFee, 2);
                    
                    Log::info('Applied late fee to transaction', [
                        'transaction_id' => $transaction->id,
                        'loan_id' => $loanId,
                        'late_fee' => $transaction->late_fee,
                        'max_consecutive_missed' => $maxConsecutiveMissed
                    ]);
                } else {
                    // Paid within 3-day grace period (or on 3rd day), no late fee
                    $transaction->late_fee = 0;
                }
                
                $transaction->save();
            }
        }
        
        Log::info('Transaction status update completed');
    }
}

