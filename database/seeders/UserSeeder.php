<?php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@futurefactory.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        
        // Create mechanic user
        User::create([
            'name' => 'Mechanic User',
            'email' => 'mechanic@futurefactory.com',
            'password' => Hash::make('password'),
            'role' => 'mechanic'
        ]);
        
        // Create planner user
        User::create([
            'name' => 'Planner User',
            'email' => 'planner@futurefactory.com',
            'password' => Hash::make('password'),
            'role' => 'planner'
        ]);
        
        // Create purchaser user
        User::create([
            'name' => 'Purchaser User',
            'email' => 'purchaser@futurefactory.com',
            'password' => Hash::make('password'),
            'role' => 'purchaser'
        ]);
        
        // Create customer user
        User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer'
        ]);
    }
}
namespace Database\Seeders;

use App\Models\User;
use App\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (UserRole::values() as $role) {
            User::factory()->create([
                'name' => fake()->name(),
                'role' => $role,
                'email' => $role . '@futurefactory.com',
                'password' => bcrypt('password')
            ]);
        }
    }
}
