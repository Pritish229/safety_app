<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FirstAidCase extends Model
{
    use HasFactory;

    protected $table = 'first_aid_cases';

    protected $fillable = [
        'user_id',
        'project_id',
        'description',
        'location',
        'victim_name',
        'employee_id',
        'location_in_charge',
        'treatment_given_by',
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
        static::deleting(function ($case) {
            if ($case->photo) {
                $path = public_path($case->photo);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
        });
    }
}
