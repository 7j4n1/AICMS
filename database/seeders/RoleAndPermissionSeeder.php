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
            //check if permission exists
        if(!Permission::where('name', 'can edit')->first())
            Permission::create(['guard_name' => 'admin', 'name' => 'can edit']);
        if(!Permission::where('name', 'can create')->first())
            Permission::create(['guard_name' => 'admin', 'name' => 'can create']);
        if(!Permission::where('name', 'can delete')->first())
            Permission::create(['guard_name' => 'admin', 'name' => 'can delete']);
        if(!Permission::where('name', 'can view')->first())
            Permission::create(['guard_name' => 'admin', 'name' => 'can view']);

        // Create a superadmin role for users authenticating with the admin guard:
        // check if role exists
        if(!Role::where('name', 'super-admin')->first()){
            $superadminRole = Role::create(['guard_name' => 'admin', 'name' => 'super-admin']);
            $superadminRole->syncPermissions(['can edit', 'can create', 'can delete', 'can view']);
        }
        // $superadminRole = Role::create(['guard_name' => 'admin', 'name' => 'super-admin'])
        //     ->syncPermissions(['can edit', 'can create', 'can delete', 'can view']);
        if(!Role::where('name', 'manager')->first()){
            $managerRole = Role::create(['guard_name' => 'admin', 'name' => 'manager'])
            ->syncPermissions(['can create', 'can view']);
        }
        

    

    }
}
