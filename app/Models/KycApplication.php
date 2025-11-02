<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'loan_id',
        // Personal Details
        'full_name', 'date_of_birth', 'gender', 'nationality',
        // Contact Information
        'mobile_number', 'email', 'alternate_contact',
        // Address Details
        'current_address', 'current_city', 'current_state', 'current_zip_code',
        'permanent_address', 'permanent_city', 'permanent_state', 'permanent_zip_code',
        'address_same_as_current', 'residential_status', 'years_at_current_address',
        // Employment Details
        'employment_type', 'employer_name', 'designation', 
        'monthly_income', 'other_income', 'employment_tenure_months',
        // Loan Details
        'loan_amount', 'loan_tenure_months', 'loan_purpose', 
        'interest_rate', 'estimated_emi',
        // KYC Documents
        'aadhar_number', 'pan_number', 'photograph_path',
        'address_proof_path', 'aadhar_card_path', 'pan_card_path',
        // Status
        'status', 'admin_notes', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'monthly_income' => 'decimal:2',
        'other_income' => 'decimal:2',
        'loan_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'estimated_emi' => 'decimal:2',
        'address_same_as_current' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Boot the model and auto-generate loan_id
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($application) {
            if (empty($application->loan_id)) {
                $application->loan_id = self::generateLoanId();
            }
        });
    }

    /**
     * Generate unique Loan ID in format LON001, LON002, etc.
     */
    public static function generateLoanId(): string
    {
        // Get the last loan ID
        $lastApplication = self::orderBy('id', 'desc')->first();
        
        if (!$lastApplication || empty($lastApplication->loan_id)) {
            return 'LON001';
        }
        
        // Extract the number from last loan ID (LON001 -> 001)
        $lastNumber = (int) substr($lastApplication->loan_id, 3);
        $newNumber = $lastNumber + 1;
        
        // Format with leading zeros (LON001, LON002, etc.)
        return 'LON' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function referencePhones()
    {
        return $this->hasMany(UserReferencePhone::class, 'loan_id', 'loan_id');
    }

    public function locations()
    {
        return $this->hasMany(UserLocation::class, 'loan_id', 'loan_id');
    }

    // Calculate EMI
    public function calculateEMI()
    {
        $P = $this->loan_amount;
        $r = ($this->interest_rate ?? 10) / 12 / 100;
        $n = $this->loan_tenure_months;
        
        if ($r == 0) {
            return $P / $n;
        }
        
        $emi = $P * $r * pow(1 + $r, $n) / (pow(1 + $r, $n) - 1);
        return round($emi, 2);
    }

    // Get status badge color
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'under_review' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }
}
