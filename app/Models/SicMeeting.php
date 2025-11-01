<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SicMeeting extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'discussed_points',
        'photo',
    ];

    // ADD THIS METHOD
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Optional: format date
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M Y, h:i A');
    }
}