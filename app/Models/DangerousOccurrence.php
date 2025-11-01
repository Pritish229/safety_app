<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DangerousOccurrence extends Model
{
    use HasFactory;

    protected $table = 'dangerous_occurrences';

    protected $fillable = [
        'user_id',
        'project_id',
        'description',
        'location',
        'reporting_person',
        'employee_id',
        'location_in_charge',
        'worst_case_outcome',
        'action_taken',
        'photo',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    protected static function booted()
    {
        static::deleting(function ($occurrence) {
            if ($occurrence->photo) {
                $path = public_path($occurrence->photo);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
        });
    }
}
