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

        // Get transaction statistics with calculations
        $loan->total_paid = $loan->transactions->where('status', 'completed')->sum('amount');
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

        // Check if already paid
        if ($transaction->status === 'completed') {
            return back()->with('error', 'This transaction has already been paid.');
        }

        // Update transaction with payment details
        $transaction->status = 'completed';
        $transaction->paid_date = $validated['payment_date'] ?? Carbon::today();
        $transaction->notes = $validated['notes'] ?? null;
        
        // Update amount if partial payment (though we expect full payment)
        if ($validated['paid_amount'] < ($transaction->amount + $transaction->late_fee)) {
            $transaction->notes = ($transaction->notes ? $transaction->notes . ' | ' : '') . 
                                 'Partial payment: ₹' . $validated['paid_amount'] . ' (Expected: ₹' . 
                                 round($transaction->amount + $transaction->late_fee, 2) . ')';
        }
        
        $transaction->save();

        // Check if loan is completed (all transactions paid)
        $loan = LoanDetail::find($loanId);
        if ($loan) {
            $loan->markAsCompleted();
        }

        return back()->with('success', 'Payment recorded successfully!');
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
            ->where('due_date', '<', Carbon::today())
            ->with('loanDetail')
            ->get();

        foreach ($pendingTransactions as $transaction) {
            $dueDate = Carbon::parse($transaction->due_date)->startOfDay();
            $today = Carbon::today();
            
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

