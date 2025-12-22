<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Health & Wellness',
                'slug' => Str::slug('Health & Wellness'),
                'description' => 'Natural remedies, oils, and wellness products.',
                'image' => null,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Baby Care',
                'slug' => Str::slug('Baby Care'),
                'description' => 'Gentle care products crafted for babies.',
                'image' => null,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Personal Care',
                'slug' => Str::slug('Personal Care'),
                'description' => 'Everyday personal care essentials.',
                'image' => null,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}

