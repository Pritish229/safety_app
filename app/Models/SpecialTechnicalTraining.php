<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialTechnicalTraining extends Model
{
    protected $table = 'special_technical_trainings';

    protected $fillable = [
        'user_id',
        'project_id',
        'location',
        'contractor_name',
        'num_persons_attended',
        'duration_seconds',
        'topics_discussed',
        'photo',
    ];

    // ADD THIS METHOD
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M Y, h:i A');
    }
}