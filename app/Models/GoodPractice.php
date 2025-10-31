<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoodPractice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location',
        'responsible_person',
        'description',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
