<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'desc',
        'status',
        'project_code',
        'photo',
        'emergency_contact',
        'site_manager_id',
    ];

    public function siteManager()
    {
        return $this->belongsTo(User::class, 'site_manager_id');
    }

    public function siteOfficers()
    {
        return $this->belongsToMany(User::class, 'project_site_officer', 'project_id', 'site_officer_id');
    }
}
