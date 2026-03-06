<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
     

        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            ExpenseCategorySeeder::class,
            // CategorySeeder::class,
            // ProductSeeder::class,
        ]);

        User::create([
            'name' => 'Kazi Rayhan Reza',
            'email' => 'thisiskazi@gmail.com',
            'phone' => null,
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}
