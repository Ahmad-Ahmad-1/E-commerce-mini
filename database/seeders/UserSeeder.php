<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => '12345678'
        ]);
        $user1->assignRole('Super Admin');

        User::factory()->create([
            'name' => 'regular user 1',
            'email' => 'regularuser1@example.com',
            'password' => '12345678'
        ]);

        User::factory()->create([
            'name' => 'regular user 2',
            'email' => 'regularuser2@example.com',
            'password' => '12345678'
        ]);
        
    }
}
