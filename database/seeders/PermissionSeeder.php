<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // 🔹 Dashboard & Profile
            ['name' => 'view-dashboard', 'description' => 'View the main dashboard'],
            ['name' => 'manage-profile', 'description' => 'Update own profile information'],

            // 🔹 User & Role Management
            ['name' => 'view-users', 'description' => 'View list of users'],
            ['name' => 'create-users', 'description' => 'Create new users'],
            ['name' => 'edit-users', 'description' => 'Edit existing users'],
            ['name' => 'delete-users', 'description' => 'Delete users'],
            ['name' => 'assign-roles', 'description' => 'Assign or remove user roles'],

            // 🔹 Role & Permission Management
            ['name' => 'view-roles', 'description' => 'View all roles'],
            ['name' => 'create-roles', 'description' => 'Create new roles'],
            ['name' => 'edit-roles', 'description' => 'Edit existing roles'],
            ['name' => 'delete-roles', 'description' => 'Delete roles'],
            ['name' => 'manage-permissions', 'description' => 'Manage permissions and role mapping'],

            // 🔹 Safety Manager Permissions
            ['name' => 'view-safety-dashboard', 'description' => 'View safety dashboard'],
            ['name' => 'manage-incidents', 'description' => 'Record and manage incidents'],
            ['name' => 'manage-safety-reports', 'description' => 'Create and review safety reports'],
            ['name' => 'inspect-sites', 'description' => 'Perform safety inspections on sites'],
            ['name' => 'assign-safety-tasks', 'description' => 'Assign safety-related tasks to site officers'],

            // 🔹 Site Officer Permissions
            ['name' => 'view-site-dashboard', 'description' => 'View site-specific dashboard'],
            ['name' => 'manage-sites', 'description' => 'Add or update site details'],
            ['name' => 'update-site-progress', 'description' => 'Report daily site progress'],
            ['name' => 'view-site-reports', 'description' => 'View reports related to site progress'],
            ['name' => 'upload-site-documents', 'description' => 'Upload required site documents'],

            // 🔹 Miscellaneous
            ['name' => 'delete-posts', 'description' => 'Delete posts or announcements'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['description' => $perm['description']]
            );
        }

        // Seed additional permissions
        $additionalPermissions = [
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'manage-permissions'
        ];

        foreach ($additionalPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
