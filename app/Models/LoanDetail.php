<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoanDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'loan_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'loan_id',
        'user_id',
        'loan_amount',
        'tenure',
        'status',
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
    ];

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to Transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'loan_id', 'loan_id');
    }

    /**
     * Calculate processing fee (5% of loan amount)
     */
    public function calculateProcessingFee()
    {
        return $this->loan_amount * 0.05; // 5%
    }

    /**
     * Calculate in-hand amount after processing fee
     */
    public function calculateInHandAmount()
    {
        return $this->loan_amount - $this->calculateProcessingFee();
    }

    /**
     * Calculate total loan amount with interest (15% per 3 months)
     */
    public function calculateTotalAmount()
    {
        // Calculate number of 3-month periods
        $periods = $this->tenure / 3;
        
        // 15% interest per 3 months
        $interestRate = 15;
        $interestAmount = $this->loan_amount * ($interestRate / 100) * $periods;
        
        return $this->loan_amount + $interestAmount;
    }

    /**
     * Calculate daily EMI amount
     */
    public function calculateDailyEMI()
    {
        $totalAmount = $this->calculateTotalAmount();
        $totalDays = $this->tenure * 30; // Assuming 30 days per month
        return $totalAmount / $totalDays;
    }

    /**
     * Calculate late fee per day (0.5% of total loan amount per day after 3 days)
     */
    public function calculateLateFeePerDay()
    {
        return $this->loan_amount * 0.005; // 0.5% per day
    }

    /**
     * Generate transactions for the loan
     */
    public function generateTransactions()
    {
        // Check if transactions already exist for this loan
        if ($this->transactions()->count() > 0) {
            return;
        }

        $dailyEMI = $this->calculateDailyEMI();
        // Use application timezone for date calculations
        $startDate = Carbon::parse($this->created_at)->setTimezone(config('app.timezone'))->toDateString();
        
        for ($i = 0; $i < $this->tenure * 30; $i++) {
            $dueDate = Carbon::parse($startDate)->setTimezone(config('app.timezone'))->addDays($i)->toDateString();
            
            Transaction::create([
                'loan_id' => $this->loan_id,
                'user_id' => $this->user_id,
                'amount' => round($dailyEMI, 2),
                'due_date' => $dueDate,
                'status' => 'pending',
            ]);
        }
    }

    /**
     * Check if loan is completed (all transactions fully paid)
     */
    public function isCompleted()
    {
        $totalTransactions = $this->transactions()->count();
        if ($totalTransactions === 0) {
            return false;
        }
        
        // Check if all transactions are fully paid (paid_amount >= amount + late_fee)
        foreach ($this->transactions as $transaction) {
            $expectedAmount = $transaction->amount + $transaction->late_fee;
            $paidAmount = $transaction->paid_amount ?? 0;
            if ($paidAmount < $expectedAmount) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Mark loan as completed if all transactions are paid
     */
    public function markAsCompleted()
    {
        if ($this->isCompleted() && $this->status !== 'completed') {
            $this->status = 'completed';
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Generate unique loan ID
     */
    public static function generateLoanId()
    {
        do {
            $loanId = 'LOAN' . strtoupper(Str::random(8));
        } while (self::where('loan_id', $loanId)->exists());
        
        return $loanId;
    }

    /**
     * Boot method to auto-generate loan ID and transactions
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($loanDetail) {
            if (empty($loanDetail->loan_id)) {
                $loanDetail->loan_id = self::generateLoanId();
            }
        });

        // Generate transactions after loan is created
        static::created(function ($loanDetail) {
            $loanDetail->generateTransactions();
        });
    }
}
