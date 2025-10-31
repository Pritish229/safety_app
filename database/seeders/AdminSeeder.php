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
        // --- 1️⃣ Run Permission Seeder ---
        $this->call(PermissionSeeder::class);

        // --- 2️⃣ Create Roles ---
        $roles = [
            'admin' => 'Full system administrator',
            'manager' => 'Manage users and roles',
            'safety_officer' => 'Handle and manage personal safety observations',
            'site_officer' => 'Can add, view, and edit safety-related records but cannot manage them',
        ];

        foreach ($roles as $key => $desc) {
            Role::updateOrCreate(
                ['name' => $key],
                ['description' => $desc]
            );
        }

        $adminRole = Role::where('name', 'admin')->first();
        $managerRole = Role::where('name', 'manager')->first();


        // 🔸 Admin → all permissions
        if ($adminRole) {
            $adminRole->permissions()->sync(Permission::pluck('id')->toArray());
        }

        // 🔸 Manager → limited to user & role management
        if ($managerRole) {
            $managerRole->permissions()->sync(
                Permission::whereIn('name', [
                    'view-dashboard',
                    'view-users',
                    'create-users',
                    'edit-users',
                    'view-roles',
                    'create-roles',
                    'edit-roles',
                ])->pluck('id')->toArray()
            );
        }


  

        // --- 4️⃣ Create Users ---

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

    

      

       

        // --- 5️⃣ Assign Roles ---
        if ($adminUser && $adminRole) $adminUser->roles()->sync([$adminRole->id]);
       
    }
}
