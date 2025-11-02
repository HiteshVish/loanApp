<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'dob',
        'gender',
        'nationality',
        'mobile',
        'email',
        'current_address',
        'permanent_address',
        'aadhar',
        'pan',
        'photo',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
