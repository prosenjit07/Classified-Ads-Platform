<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users
        $users = \App\Models\User::factory(10)->create();
        
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);
        
        // Create regular test user
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
        ]);
        
        // Call other seeders
        $this->call([
            \Database\Seeders\WishlistSeeder::class,
            // Add other seeders here
        ]);
    }
}
