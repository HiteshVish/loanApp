<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReferencePhone extends Model
{
    use HasFactory;

    protected $table = 'user_reference_phone';

    protected $fillable = [
        'user_id',
        'contact_number',
        'name',
    ];

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
