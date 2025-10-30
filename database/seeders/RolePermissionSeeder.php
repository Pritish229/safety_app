<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch roles
        $adminRole = Role::where('name', 'admin')->first();
        $safetyManagerRole = Role::where('name', 'safety manager')->first();
        $siteOfficerRole = Role::where('name', 'site officer')->first();

        $permissions = Permission::all()->pluck('id', 'name')->toArray();
        if ($adminRole) {
            $adminRole->permissions()->sync(array_values($permissions));
        }
        if ($safetyManagerRole) {
            $safetyPerms = [
                'view-dashboard',
                'view-safety-dashboard',
                'manage-incidents',
                'manage-safety-reports',
                'inspect-sites',
                'assign-safety-tasks',
                'manage-permissions',
                'view-roles',
                'view-users',
            ];

            $permIds = array_values(array_intersect_key($permissions, array_flip($safetyPerms)));
            $safetyManagerRole->permissions()->sync($permIds);
        }
        if ($siteOfficerRole) {
            $sitePerms = [
                'view-dashboard',
                'view-site-dashboard',
                'manage-sites',
                'update-site-progress',
                'view-site-reports',
                'upload-site-documents',
                'manage-profile',
            ];

            $permIds = array_values(array_intersect_key($permissions, array_flip($sitePerms)));
            $siteOfficerRole->permissions()->sync($permIds);
        }
    }
}
