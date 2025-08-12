<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-branches',
            'create-branches',
            'edit-branches',
            'delete-branches',
            'view-teachers',
            'create-teachers',
            'edit-teachers',
            'delete-teachers',
            'view-students',
            'create-students',
            'edit-students',
            'delete-students',
            'view-groups',
            'create-groups',
            'edit-groups',
            'delete-groups',
            'view-attendance',
            'create-attendance',
            'edit-attendance',
            'delete-attendance',
            'view-grades',
            'create-grades',
            'edit-grades',
            'delete-grades',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'teacher']);
        $role->givePermissionTo([
            'view-students',
            'view-groups',
            'view-attendance',
            'create-attendance',
            'edit-attendance',
            'view-grades',
            'create-grades',
            'edit-grades',
        ]);

        $role = Role::create(['name' => 'student']);
        $role->givePermissionTo([
            'view-grades',
            'view-attendance',
        ]);
    }
}
