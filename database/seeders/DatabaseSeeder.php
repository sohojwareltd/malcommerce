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
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        // Create a sample sponsor
        User::create([
            'name' => 'Sponsor User',
            'email' => 'sponsor@example.com',
            'password' => bcrypt('password'),
            'role' => 'sponsor',
        ]);

        $this->call([
            // CategorySeeder::class,
            // ProductSeeder::class,
        ]);
    }
}
