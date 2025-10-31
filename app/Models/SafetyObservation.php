<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafetyObservation extends Model
{
    use HasFactory;

    protected $table = 'safety_observations';

    protected $fillable = [
        'user_id',
        'observation',
        'location',
        'security_level',
        'responsible_person',
        'recommended_action',
        'photo_path',
        'status',
    ];

    /**
     * 🔹 Each safety observation belongs to one user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 🔹 Helper to get readable badge color for security level
     */
    public function getSecurityLevelBadgeAttribute(): string
    {
        return match ($this->security_level) {
            '1 - Low' => 'success',
            '2 - Moderate' => 'info',
            '3 - Significant' => 'warning',
            '4 - High' => 'danger',
            '5 - Critical' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * 🔹 Helper to show status text in readable format
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status ?? 'submitted');
    }

    /**
     * 🔹 Automatically delete photo if observation deleted
     */
    protected static function booted()
    {
        static::deleting(function ($observation) {
            if ($observation->photo_path && file_exists(public_path($observation->photo_path))) {
                @unlink(public_path($observation->photo_path));
            }
        });
    }
}
