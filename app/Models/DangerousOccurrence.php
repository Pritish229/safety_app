<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DangerousOccurrence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'location',
        'reporting_person',
        'employee_id',
        'location_in_charge',
        'worst_case_outcome',
        'action_taken',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
