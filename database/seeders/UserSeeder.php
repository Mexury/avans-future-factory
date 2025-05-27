<?php

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
