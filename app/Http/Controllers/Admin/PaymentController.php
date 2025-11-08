<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanDetail;
use App\Models\Transaction;
use Illuminate\Http\Request;
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
        // Get all pending transactions with past due dates
        $pendingTransactions = Transaction::where('status', 'pending')
            ->where('due_date', '<', Carbon::today(config('app.timezone')))
            ->with('loanDetail')
            ->get();

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
                }
            }
        }
    }
}

