<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrator with full access to manage all modules and settings.'
            ],
            [
                'name' => 'safety manager',
                'description' => 'Responsible for managing safety reports, incidents, and inspections.'
            ],
            [
                'name' => 'site officer',
                'description' => 'Handles site operations, progress updates, and document uploads.'
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
