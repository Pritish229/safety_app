<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FirstAidCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'location',
        'victim_name',
        'employee_id',
        'location_in_charge',
        'treatment_given_by',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
