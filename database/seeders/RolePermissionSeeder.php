<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'clock.create',
            'clock.view-own',
            'clock.view-all',
            'clock.edit',
            'dashboard.view-own',
            'dashboard.view-all',
            'reports.export',
            'users.view',
            'users.edit',
            'users.create',
            'users.manage-department',
            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions

        // User Role
        $userRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);
        $userRole->syncPermissions([
            'clock.create',
            'clock.view-own',
            'dashboard.view-own',
        ]);

        // Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions([
            'clock.view-all',
            'clock.edit',
            'dashboard.view-all',
            'reports.export',
            'users.view',
            'users.edit',
            'users.create',
            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',
        ]);

        // Department Head Role
        $deptHeadRole = Role::firstOrCreate(['name' => 'Department Head', 'guard_name' => 'web']);
        $deptHeadRole->syncPermissions([
            'clock.view-own',
            'dashboard.view-own',
            'users.manage-department',
            'departments.view',
        ]);
    }
}
