<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SicMeeting extends Model
{
    use HasFactory;

    protected $table = 'sic_meetings';

    protected $fillable = [
        'user_id',
        'date_time',
        'discussed_points',
        'photo_path',
        'status',
    ];

    /**
     * 🔹 Each meeting belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 🔹 Readable status
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status ?? 'submitted');
    }

    /**
     * 🔹 Delete attached photo when record is deleted
     */
    protected static function booted()
    {
        static::deleting(function ($meeting) {
            if ($meeting->photo_path && file_exists(public_path($meeting->photo_path))) {
                @unlink(public_path($meeting->photo_path));
            }
        });
    }
}
