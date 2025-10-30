<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1️⃣ Run Permission Seeder first ---
        $this->call(PermissionSeeder::class);

        // --- 2️⃣ Define Roles ---
        $roles = [
            'admin' => 'Manage everything in the system',
            'safety_manager' => 'Oversee safety operations and reports',
            'site_officer' => 'Handle day-to-day site activities',
        ];

        foreach ($roles as $key => $desc) {
            Role::updateOrCreate(
                ['name' => $key],
                ['description' => $desc]
            );
        }

        // --- 3️⃣ Get Roles ---
        $adminRole = Role::where('name', 'admin')->first();
        $safetyRole = Role::where('name', 'safety_manager')->first();
        $siteRole = Role::where('name', 'site_officer')->first();

        // --- 4️⃣ Assign Permissions to Roles ---
        // Admin gets all permissions
        if ($adminRole) {
            $adminRole->permissions()->sync(Permission::pluck('id')->toArray());
        }

        // Safety Manager permissions
        if ($safetyRole) {
            $safetyRole->permissions()->sync(
                Permission::whereIn('name', [
                    'view-dashboard',
                    'view-safety-dashboard',
                    'manage-incidents',
                    'manage-safety-reports',
                    'inspect-sites',
                    'assign-safety-tasks',
                ])->pluck('id')->toArray()
            );
        }

        // Site Officer permissions
        if ($siteRole) {
            $siteRole->permissions()->sync(
                Permission::whereIn('name', [
                    'view-dashboard',
                    'view-site-dashboard',
                    'manage-sites',
                    'update-site-progress',
                    'view-site-reports',
                    'upload-site-documents',
                ])->pluck('id')->toArray()
            );
        }

        // --- 5️⃣ Create Users ---
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password123'),
                'gender' => 'male',
                'image' => 'profiles/admin_default.jpg',
                'email_verified_at' => now(),
            ]
        );

        $safetyUser = User::updateOrCreate(
            ['email' => 'safety@example.com'],
            [
                'name' => 'Safety Manager',
                'password' => bcrypt('password123'),
                'gender' => 'male',
                'image' => 'profiles/safety_default.jpg',
                'email_verified_at' => now(),
            ]
        );

        $siteUser = User::updateOrCreate(
            ['email' => 'site@example.com'],
            [
                'name' => 'Site Officer',
                'password' => bcrypt('password123'),
                'gender' => 'female',
                'image' => 'profiles/site_default.jpg',
                'email_verified_at' => now(),
            ]
        );

        // --- 6️⃣ Assign Roles to Users ---
        if ($adminUser && $adminRole) $adminUser->roles()->sync([$adminRole->id]);
        if ($safetyUser && $safetyRole) $safetyUser->roles()->sync([$safetyRole->id]);
        if ($siteUser && $siteRole) $siteUser->roles()->sync([$siteRole->id]);
    }
}
