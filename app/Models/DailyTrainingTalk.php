<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTrainingTalk extends Model
{
    use HasFactory;

    protected $table = 'daily_training_talks';

    protected $fillable = [
        'user_id',
        'project_id',
        'location',
        'contractor_name',
        'number_of_persons',
        'duration_seconds',
        'topics_discussed',
        'photo_path',
        'status',
    ];

    /**
     * 🔹 Each DTT belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 🔹 Each DTT belongs to a project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * 🔹 Accessor for a readable status label
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status ?? 'Submitted');
    }

    /**
     * 🔹 Automatically delete uploaded photo when record is deleted
     */
    protected static function booted()
    {
        static::deleting(function ($talk) {
            if ($talk->photo_path) {
                $path = public_path($talk->photo_path);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
        });
    }
}
