<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InductionTraining extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location',
        'contractor_name',
        'num_persons_attended',
        'duration_seconds',
        'notes',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
