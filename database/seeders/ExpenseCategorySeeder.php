<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Marketing', 'slug' => 'marketing', 'description' => 'Advertising, promotions, campaigns', 'sort_order' => 1],
            ['name' => 'Purchasing', 'slug' => 'purchasing', 'description' => 'Inventory, supplies, stock', 'sort_order' => 2],
            ['name' => 'Salary', 'slug' => 'salary', 'description' => 'Employee wages and salaries', 'sort_order' => 3],
            ['name' => 'Maintenance', 'slug' => 'maintenance', 'description' => 'Repairs, upkeep, servicing', 'sort_order' => 4],
            ['name' => 'Utilities', 'slug' => 'utilities', 'description' => 'Electricity, water, internet', 'sort_order' => 5],
            ['name' => 'Rent', 'slug' => 'rent', 'description' => 'Rent and lease payments', 'sort_order' => 6],
            ['name' => 'Other', 'slug' => 'other', 'description' => 'Miscellaneous expenses', 'sort_order' => 99],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
