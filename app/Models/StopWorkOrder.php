<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StopWorkOrder extends Model
{
    use HasFactory;

    protected $table = 'stop_work_orders';

    protected $fillable = [
        'user_id',
        'date_time',
        'observation',
        'location',
        'security_level',
        'concerned_supervisor',
        'swo_issued_for',
        'recommended_action',
        'photo_path',
        'status',
    ];

    /**
     * 🔹 Relationship: Each SWO belongs to one user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 🔹 Accessor for status label
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
        static::deleting(function ($swo) {
            if ($swo->photo_path && file_exists(public_path($swo->photo_path))) {
                @unlink(public_path($swo->photo_path));
            }
        });
    }
}
