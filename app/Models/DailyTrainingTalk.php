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
        'location',
        'contractor_name',
        'number_of_persons',
        'duration_seconds',
        'topics_discussed',
        'photo_path',
        'status',
    ];

    /**
     * 🔹 Each DTT report belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 🔹 Accessor for readable status label
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status ?? 'submitted');
    }

    /**
     * 🔹 Automatically delete uploaded photo when record is deleted
     */
    protected static function booted()
    {
        static::deleting(function ($talk) {
            if ($talk->photo_path && file_exists(public_path($talk->photo_path))) {
                @unlink(public_path($talk->photo_path));
            }
        });
    }
}
