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
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'shadiomondi22@gmail.com',
            'password' => bcrypt('12345678'),
            'is_admin' => true,
        ]);

        // Seed products
        $this->call(ProductSeeder::class);
    }
}
