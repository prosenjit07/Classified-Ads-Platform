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
        
        // Create admin user with secure password
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'), // Using a secure password
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);
        
        // Assign admin role (if using Spatie Permission package)
        if (class_exists('\Spatie\Permission\Models\Role')) {
            $admin->assignRole('admin');
        }
        
        // Create regular test user
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'), // Using a secure password
            'email_verified_at' => now(),
        ]);
        
        // Call other seeders
        $this->call([
            \Database\Seeders\WishlistSeeder::class,
            // Add other seeders here
        ]);
    }
}
