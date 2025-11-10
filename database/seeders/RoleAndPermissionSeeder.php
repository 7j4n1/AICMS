<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
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

        // define all the guards
        $guards = ['admin', 'user', 'web', 'api'];

        // define permissions to be created
        $permissions = [
            'can edit',
            'can create',
            'can delete',
            'can view',
            'can only view',
            'configure-system'
        ];

        // create permissions for all guards
        foreach ($guards as $guard) {
            foreach ($permissions as $permission) {
                // check if permission exists
                $this->createPermissionIfNotExists($permission, $guard);
            }
        }
        
        // Create roles and assign existing permissions
        $this->createRoles();
        // Create a default super admin user if not exists
        $this->createDefaultAdminUser();
    }

    private function createDefaultAdminUser()
    {
        $admin = \App\Models\Admin::where('username', 'superadmin')->first();

        if(!$admin){
            $admin = \App\Models\Admin::create([
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => '',
                'password' => Hash::make('password1234455'),
            ]);
            // Assign role for admin guard
            $admin->assignRole('super-admin');

            $adminRoleApi = Role::where('name', 'super-admin')->where('guard_name', 'api')->first();
            if ($adminRoleApi) {
                $admin->roles()->attach($adminRoleApi->id);
            }
        }
    }

    private function createPermissionIfNotExists($name, $guard)
    {
        if (!Permission::where('name', $name)->where('guard_name', $guard)->exists()) {
            Permission::create(['name' => $name, 'guard_name' => $guard]);
        }
    }

    private function createRoles()
    {
        $roleConfigs = [
            'super-admin' => [
                'guard' => ['admin', 'api'],
                'permissions' => ['can edit', 'can create', 'can delete', 'can view', 'configure-system'],
            ],
            'admin' => [
                'guard' => ['admin', 'web', 'api'],
                'permissions' => ['can edit', 'can create', 'can delete', 'can view'],
            ],
            'manager' => [
                'guard' => ['admin', 'api'],
                'permissions' => ['can create', 'can view', 'can edit'],
            ],
            'member' => [
                'guard' => ['admin', 'web', 'api'],
                'permissions' => ['can only view'],
            ],
        ];

        foreach ($roleConfigs as $roleName => $config) {
            foreach ($config['guard'] as $guard) {
                $this->createRolesIfNotExists($roleName, $guard, $config['permissions']);
            }
        }
        
    }

    private function createRolesIfNotExists(string $roleName, string $guard, array $permissions_config)
    {
        // check if role exists
        if (!Role::where('name', $roleName)->where('guard_name', $guard)->exists()) {
            $role = Role::create(['name' => $roleName, 'guard_name' => $guard]);

            // Get permissions for the specified guard
            $permissions = Permission::where('guard_name', $guard)
                ->whereIn('name', $permissions_config)
                ->get();

            // Sync permissions with the role
            $role->syncPermissions($permissions);
        }
    }
}