<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoodPractice extends Model
{
    use HasFactory;

    protected $table = 'good_practices';

    protected $fillable = [
        'user_id',
        'project_id',
        'location',
        'responsible_person',
        'description',
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
        static::deleting(function ($practice) {
            if ($practice->photo) {
                $path = public_path($practice->photo);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
        });
    }
}
