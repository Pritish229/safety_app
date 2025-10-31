<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'view-dashboard', 'description' => 'View the main dashboard'],

            // 🔹 User Management
            ['name' => 'view-users', 'description' => 'View list of users'],
            ['name' => 'create-users', 'description' => 'Create new users'],
            ['name' => 'edit-users', 'description' => 'Edit existing users'],
            ['name' => 'delete-users', 'description' => 'Delete users'],

            // 🔹 Role Management
            ['name' => 'view-roles', 'description' => 'View all roles'],
            ['name' => 'create-roles', 'description' => 'Create new roles'],
            ['name' => 'edit-roles', 'description' => 'Edit existing roles'],
            ['name' => 'delete-roles', 'description' => 'Delete roles'],


            // 🔹 Safety Observation
            ['name' => 'view_own_safety_observation', 'description' => 'View own safety observations'],
            ['name' => 'edit_own_safety_observation', 'description' => 'Edit own safety observations'],
            ['name' => 'manage_own_safety_observation', 'description' => 'Manage or update own safety observations'],
            ['name' => 'add_own_safety_observation', 'description' => 'Add new safety observations'],

            // 🔹 Daily Training Talk (DTT)
            ['name' => 'view_own_DTT', 'description' => 'View own daily training talks'],
            ['name' => 'manage_DTT', 'description' => 'Manage daily training talks'],
            ['name' => 'add_own_DTT', 'description' => 'Add new daily training talks'],
            ['name' => 'edit_own_DTT', 'description' => 'Edit own daily training talks'],

            // 🔹 Special Technical Training (STT)
            ['name' => 'view_own_STT', 'description' => 'View own special technical trainings'],
            ['name' => 'manage_STT', 'description' => 'Manage special technical trainings'],
            ['name' => 'add_own_STT', 'description' => 'Add new special technical trainings'],
            ['name' => 'edit_own_STT', 'description' => 'Edit own special technical trainings'],

            // 🔹 Stop Work Order (SWO)
            ['name' => 'view_own_SWO', 'description' => 'View own stop work orders'],
            ['name' => 'manage_SWO', 'description' => 'Manage stop work orders'],
            ['name' => 'add_own_SWO', 'description' => 'Add new stop work orders'],
            ['name' => 'edit_own_SWO', 'description' => 'Edit own stop work orders'],

            // 🔹 Near Miss Report (NMR)
            ['name' => 'view_own_NMR', 'description' => 'View own near miss reports'],
            ['name' => 'manage_NMR', 'description' => 'Manage near miss reports'],
            ['name' => 'add_own_NMR', 'description' => 'Add new near miss reports'],
            ['name' => 'edit_own_NMR', 'description' => 'Edit own near miss reports'],

            // 🔹 SIC Meeting
            ['name' => 'view_own_SIC_meetings', 'description' => 'View own SIC meetings'],
            ['name' => 'manage_SIC_meetings', 'description' => 'Manage SIC meetings'],
            ['name' => 'add_own_SIC_meetings', 'description' => 'Add new SIC meetings'],
            ['name' => 'edit_own_SIC_meetings', 'description' => 'Edit own SIC meetings'],

            // 🔹 Induction Training
            ['name' => 'view_own_induction_training', 'description' => 'View own induction trainings'],
            ['name' => 'manage_induction_training', 'description' => 'Manage induction trainings'],
            ['name' => 'add_own_induction_training', 'description' => 'Add new induction trainings'],
            ['name' => 'edit_own_induction_training', 'description' => 'Edit own induction trainings'],

            // 🔹 Pep Talk
            ['name' => 'view_own_pep_talk', 'description' => 'View own pep talks'],
            ['name' => 'manage_pep_talk', 'description' => 'Manage pep talks'],
            ['name' => 'add_own_pep_talk', 'description' => 'Add new pep talks'],
            ['name' => 'edit_own_pep_talk', 'description' => 'Edit own pep talks'],

            // 🔹 SAW
            ['name' => 'view_own_saw', 'description' => 'View own saws'],
            ['name' => 'manage_saw', 'description' => 'Manage saws'],
            ['name' => 'add_own_saw', 'description' => 'Add new saws'],
            ['name' => 'edit_own_saw', 'description' => 'Edit own saws'],

            // 🔹 First Aid Case
            ['name' => 'view_own_first_aid_case', 'description' => 'View own first aid cases'],
            ['name' => 'manage_first_aid_case', 'description' => 'Manage first aid cases'],
            ['name' => 'add_own_first_aid_case', 'description' => 'Add new first aid cases'],
            ['name' => 'edit_own_first_aid_case', 'description' => 'Edit own first aid cases'],

            // 🔹 Dangerous Occurrence
            ['name' => 'view_own_dangerous_occurrence', 'description' => 'View own dangerous occurrences'],
            ['name' => 'manage_dangerous_occurrence', 'description' => 'Manage dangerous occurrences'],
            ['name' => 'add_own_dangerous_occurrence', 'description' => 'Add new dangerous occurrences'],
            ['name' => 'edit_own_dangerous_occurrence', 'description' => 'Edit own dangerous occurrences'],

            // 🔹 Good Practice
            ['name' => 'view_own_good_practice', 'description' => 'View own good practices'],
            ['name' => 'manage_good_practice', 'description' => 'Manage good practices'],
            ['name' => 'add_own_good_practice', 'description' => 'Add new good practices'],
            ['name' => 'edit_own_good_practice', 'description' => 'Edit own good practices'],


        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['description' => $perm['description']]
            );
        }
    }
}
