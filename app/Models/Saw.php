<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Saw extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'observation',
        'location',
        'security_level',
        'work_supervisor',
        'swo_issued_for',
        'recommended_action',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
