<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'role',
        'kyc_status',
        'from_register',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * KYC Application relationship
     */
    public function kycApplication()
    {
        return $this->hasOne(\App\Models\KycApplication::class);
    }

    /**
     * User Reference Phone relationship
     */
    public function referencePhones()
    {
        return $this->hasMany(\App\Models\UserReferencePhone::class);
    }

    /**
     * User Location relationship
     */
    public function locations()
    {
        return $this->hasMany(\App\Models\UserLocation::class);
    }

    /**
     * User Detail relationship
     */
    public function userDetail()
    {
        return $this->hasOne(\App\Models\UserDetail::class);
    }

    /**
     * Loan Details relationship
     */
    public function loanDetails()
    {
        return $this->hasMany(\App\Models\LoanDetail::class);
    }

    /**
     * Check if user has submitted KYC
     *
     * @return bool
     */
    public function hasSubmittedKyc(): bool
    {
        return $this->kyc_status !== 'not_submitted';
    }

    /**
     * Check if user's KYC is approved
     *
     * @return bool
     */
    public function isKycApproved(): bool
    {
        return $this->kyc_status === 'approved';
    }

    /**
     * Check if user's KYC is pending
     *
     * @return bool
     */
    public function isKycPending(): bool
    {
        return in_array($this->kyc_status, ['pending', 'under_review']);
    }

    /**
     * Check if user's KYC is rejected
     *
     * @return bool
     */
    public function isKycRejected(): bool
    {
        return $this->kyc_status === 'rejected';
    }

    /**
     * Check if user submitted KYC from register
     *
     * @return bool
     */
    public function hasSubmittedFromRegister(): bool
    {
        return $this->from_register == 1;
    }

    /**
     * User Location relationship
     */
    // Removed: User locations now linked to loan_id in KycApplication
}
