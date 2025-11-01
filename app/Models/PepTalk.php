<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PepTalk extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'location',
        'contractor_name',
        'num_persons_attended',
        'duration_seconds',
        'topics_discussed',
        'photo'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
