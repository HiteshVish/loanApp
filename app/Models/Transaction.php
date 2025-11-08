<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'user_id',
        'amount',
        'paid_amount',
        'due_date',
        'paid_date',
        'status',
        'late_fee',
        'days_late',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    /**
     * Relationship to LoanDetail
     */
    public function loanDetail()
    {
        return $this->belongsTo(LoanDetail::class, 'loan_id', 'loan_id');
    }

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate late fee based on consecutive missed days
     * Late fee starts only after 3 consecutive missed payments: 0.5% of loan amount per day
     * Key rule: User must miss 3 consecutive payments in a row to incur late fee
     */
    public function calculateLateFee($loanAmount, $consecutiveMissedDays)
    {
        if ($consecutiveMissedDays <= 3) {
            return 0; // No late fee if 3 or fewer consecutive missed days
        }
        
        // 0.5% per day AFTER 3 consecutive missed days
        $lateFeeRate = 0.005; // 0.5%
        $effectiveDaysLate = $consecutiveMissedDays - 3;
        return $loanAmount * $lateFeeRate * $effectiveDaysLate;
    }

    /**
     * Get consecutive missed days count before this transaction
     */
    public function getConsecutiveMissedDays()
    {
        $consecutiveMissed = 0;
        
        // Get previous transactions ordered by due_date DESC to count backwards
        $previousTransactions = Transaction::where('loan_id', $this->loan_id)
            ->where('due_date', '<', $this->due_date)
            ->orderBy('due_date', 'desc')
            ->get();
        
        // Count consecutive missed transactions before this one
        foreach ($previousTransactions as $prevTransaction) {
            if ($prevTransaction->status !== 'completed') {
                $consecutiveMissed++;
            } else {
                // If we find a completed payment, stop counting
                break;
            }
        }
        
        return $consecutiveMissed;
    }

    /**
     * Calculate total amount due (EMI + late fee)
     */
    public function calculateTotalDue()
    {
        $total = $this->amount;
        if ($this->late_fee > 0) {
            $total += $this->late_fee;
        }
        return $total;
    }

    /**
     * Update transaction status based on current date and consecutive missed payments
     */
    public function updateStatus()
    {
        // Use application timezone for date comparisons
        $today = Carbon::today(config('app.timezone'));
        $dueDate = Carbon::parse($this->due_date)->setTimezone(config('app.timezone'))->startOfDay();
        
        if ($this->status === 'completed') {
            return; // Already completed
        }
        
        // Only mark as delayed if due date is in the past
        if ($dueDate->lt($today)) {
            // Calculate days late (positive number for past dates)
            $daysLate = $dueDate->diffInDays($today, false);
            
            if ($daysLate > 0) {
                $this->days_late = $daysLate;
                
                // Get consecutive missed days before this transaction
                $consecutiveMissed = $this->getConsecutiveMissedDays();
                
                // Calculate late fee based on consecutive missed days
                $calculatedFee = $this->calculateLateFee($this->loanDetail->loan_amount, $consecutiveMissed);
                $this->late_fee = $calculatedFee > 0 ? (float)round($calculatedFee, 2) : 0;
                $this->status = 'delayed';
            } else {
                $this->status = 'pending';
            }
        } else {
            // Future dates should remain pending
            $this->status = 'pending';
        }
        
        $this->save();
    }
}
