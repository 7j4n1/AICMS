<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        // create permissions
        // Create a permission for the superadmin role:
            Permission::create(['guard_name' => 'admin', 'name' => 'can edit']);
            Permission::create(['guard_name' => 'admin', 'name' => 'can create']);
            Permission::create(['guard_name' => 'admin', 'name' => 'can delete']);
            Permission::create(['guard_name' => 'admin', 'name' => 'can view']);

        // Create a superadmin role for users authenticating with the admin guard:
        $superadminRole = Role::create(['guard_name' => 'admin', 'name' => 'super-admin'])
            ->syncPermissions(['can edit', 'can create', 'can delete', 'can view']);

        $managerRole = Role::create(['guard_name' => 'admin', 'name' => 'manager'])
            ->syncPermissions(['can create', 'can view']);

    

    }
}
