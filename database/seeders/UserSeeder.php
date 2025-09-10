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

        // Each category has a specific departement for it.

        $user2 = User::factory()->create([
            'name' => 'Seller 1',
            'email' => 'seller1@example.com',
            'password' => '12345678'
        ]);
        $user2->assignRole('Super Admin');

        $user3 = User::factory()->create([
            'name' => 'Seller 2',
            'email' => 'seller2@example.com',
            'password' => '12345678'
        ]);
        $user3->assignRole('Super Admin');

        User::factory()->create([
            'name' => 'regular user',
            'email' => 'regularuser1@example.com',
            'password' => '12345678'
        ]);
    }
}
