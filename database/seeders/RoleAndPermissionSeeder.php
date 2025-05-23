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
        // if(!Permission::where('name', 'can view only')->first())
        //     Permission::create(['guard_name' => 'admin', 'name' => 'can view only']);
        if(!Permission::where('name', 'can only view')->first())
            Permission::create(['guard_name' => 'user', 'name' => 'can only view']);
        
        // check if permission exists for the guard 'web'
        if(!Permission::where('name', 'can only view')->where('guard_name', 'web')->exists())
            Permission::create(['guard_name' => 'web', 'name' => 'can only view']);

        // check if permission exists for the guard 'web'
        if(!Permission::where('name', 'can only view')->where('guard_name', 'admin')->exists())
            Permission::create(['guard_name' => 'admin', 'name' => 'can only view']);

        // Create a superadmin role for users authenticating with the admin guard:
        // check if role exists
        if(!Role::where('name', 'super-admin')->first()){
            $superadminRole = Role::create(['guard_name' => 'admin', 'name' => 'super-admin']);
            $superadminRole->syncPermissions(['can edit', 'can create', 'can delete', 'can view']);
        }
        // $superadminRole = Role::create(['guard_name' => 'admin', 'name' => 'super-admin'])
        //     ->syncPermissions(['can edit', 'can create', 'can delete', 'can view']);
        if(!Role::where('name', 'manager')->first()){
            $managerRole = Role::create(['guard_name' => 'admin', 'name' => 'manager']);
            $managerRole->syncPermissions(['can create', 'can view', 'can edit']);
        }
        // member role
        if(!Role::where('name', 'member')->first()){
            $memberRole = Role::create(['guard_name' => 'user', 'name' => 'member']);
            $memberRole->syncPermissions(['can only view']);
        }

        // check if role exists for the guard 'web'
        if(!Role::where('name', 'member')->where('guard_name', 'web')->exists()){
            $memberRole = Role::create(['guard_name' => 'web', 'name' => 'member']);
            $memberRole->syncPermissions(['can only view']);
        }

        if(!Role::where('name', 'member')->where('guard_name', 'admin')->exists()){
            $memberRole = Role::create(['guard_name' => 'admin', 'name' => 'member']);
            $memberRole->syncPermissions(['can only view']);
        }
        
        $user = \App\Models\Admin::where('username', 'superadmin')->first();

        if(!$user){
            \App\Models\Admin::create([
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => '',
                'password' => Hash::make('password0987'),
            ])->assignRole('super-admin');
        }

    }
}
