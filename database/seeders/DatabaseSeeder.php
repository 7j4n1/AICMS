<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        $user = \App\Models\Admin::where('username', 'superadmin')->first();

        if(!$user){
            \App\Models\Admin::factory()->create([
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => '',
                'password' => Hash::make('password0987'),
            ])->assignRole('super-admin');
        }
        

        // \App\Models\Admin::factory()->create()->assignRole('manager');

    }
}
