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
            'email' => null,
            'phone' => '8801795560431',
            'password' => null,
            'role' => 'admin',
        ]);

        $this->call([
            // CategorySeeder::class,
            // ProductSeeder::class,
        ]);
    }
}
