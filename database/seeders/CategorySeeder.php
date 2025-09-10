<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::factory()->create([
            'category_name' => 'Food',
        ]);
        Category::factory()->create([
            'category_name' => 'Drink',
        ]);
        Category::factory()->create([
            'category_name' => 'PC',
        ]);
        Category::factory()->create([
            'category_name' => 'Phone',
        ]);
        Category::factory()->create([
            'category_name' => 'Clothes',
        ]);
    }
}
