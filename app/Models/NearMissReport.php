<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NearMissReport extends Model
{
    use HasFactory;

    protected $table = 'near_miss_reports';

    protected $fillable = [
        'user_id',
        'date_time',
        'description',
        'location',
        'person_involved',
        'contractor_name',
        'location_in_charge',
        'worst_case_outcome',
        'action_taken',
        'photo_path',
        'status',
    ];

    /**
     * 🔹 Relationship: Each report belongs to one user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 🔹 Accessor for readable status
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status ?? 'submitted');
    }

    /**
     * 🔹 Auto-delete photo when record is removed
     */
    protected static function booted()
    {
        static::deleting(function ($report) {
            if ($report->photo_path && file_exists(public_path($report->photo_path))) {
                @unlink(public_path($report->photo_path));
            }
        });
    }
}
