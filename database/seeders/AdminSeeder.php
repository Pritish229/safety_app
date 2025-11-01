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
        $this->call(PermissionSeeder::class);

        $roles = [
            'admin' => 'Can manage users, roles, and permissions only',
            'manager' => 'Manage users and roles',
            'safety_officer' => 'Handle and manage personal safety observations',
            'site_officer' => 'Can handle STT, SIC Meetings, Induction Training, and Pep Talk',
            'site_manager' => 'Can manage site officers, site activities, and training reports',
        ];

        foreach ($roles as $key => $desc) {
            Role::updateOrCreate(['name' => $key], ['description' => $desc]);
        }

        $adminRole = Role::where('name', 'admin')->first();
        $siteOfficerRole = Role::where('name', 'site_officer')->first();
        $siteManagerRole = Role::where('name', 'site_manager')->first();

        /**
         * === ADMIN ===
         */
        if ($adminRole) {
            $adminRole->permissions()->sync(
                Permission::whereIn('name', [
                    'view-dashboard',
                    'view-users', 'create-users', 'edit-users', 'delete-users',
                    'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
                ])->pluck('id')->toArray()
            );
        }

        /**
         * === SITE MANAGER ===
         */
        if ($siteManagerRole) {
            $siteManagerPermissions = Permission::whereIn('name', [
                'view-dashboard',
                'view_projects', 'add_projects', 'edit_projects', 'delete_projects',
                'assign_site_officers',
                'view_own_STT', 'manage_STT',
                'view_own_SIC_meetings', 'manage_SIC_meetings',
                'view_own_induction_training', 'manage_induction_training',
                'view_own_pep_talk', 'manage_pep_talk',
            ])->pluck('id')->toArray();

            $siteManagerRole->permissions()->sync($siteManagerPermissions);
        }

        /**
         * === SITE OFFICER ===
         */
        if ($siteOfficerRole) {
            $siteOfficerPermissions = Permission::whereIn('name', [
                'view-dashboard',
                'view_projects',
                'view_own_STT', 'add_own_STT', 'edit_own_STT',
                'view_own_SIC_meetings', 'add_own_SIC_meetings', 'edit_own_SIC_meetings',
                'view_own_induction_training', 'add_own_induction_training', 'edit_own_induction_training',
                'view_own_pep_talk', 'add_own_pep_talk', 'edit_own_pep_talk',
            ])->pluck('id')->toArray();

            $siteOfficerRole->permissions()->sync($siteOfficerPermissions);
        }


        /**
         * === USER CREATION ===
         */
        $users = [
            'admin@example.com' => ['Admin User', 'admin', $adminRole],
            'sitemanager@example.com' => ['Abhinash Pani', 'site_manager', $siteManagerRole],
            'siteofficer@example.com' => ['Pritish Dash', 'site_officer', $siteOfficerRole],
        ];

        foreach ($users as $email => [$name, $gender, $role]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => bcrypt('password123'),
                    'gender' => 'male',
                    'image' => 'profiles/default.jpg',
                    'email_verified_at' => now(),
                ]
            );

            if ($role && $user) {
                $user->roles()->sync([$role->id]);
            }
        }
    }
}
