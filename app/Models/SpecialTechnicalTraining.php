<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialTechnicalTraining extends Model
{
    use HasFactory;

    protected $table = 'special_technical_trainings';

    protected $fillable = [
        'user_id',
        'location',
        'contractor_name',
        'number_of_persons',
        'duration_seconds',
        'topics_name',
        'photo_path',
        'status',
    ];

    /**
     * 🔹 Each training belongs to a user
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
     * 🔹 Automatically remove uploaded photo when deleted
     */
    protected static function booted()
    {
        static::deleting(function ($training) {
            if ($training->photo_path && file_exists(public_path($training->photo_path))) {
                @unlink(public_path($training->photo_path));
            }
        });
    }
}
