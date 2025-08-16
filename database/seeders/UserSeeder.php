<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user with secure password
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('admin123'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );
        
        // Assign admin role (if using Spatie Permission package)
        if (class_exists('\Spatie\Permission\Models\Role')) {
            $admin->assignRole('admin');
        }
        
        // Create regular test user
        $testUser = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'email' => 'user@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        
        // Create additional test users only if they don't exist
        $existingUsersCount = User::whereNotIn('email', ['admin@example.com', 'user@example.com'])->count();
        
        if ($existingUsersCount < 10) {
            $usersToCreate = 10 - $existingUsersCount;
            User::factory($usersToCreate)->create();
        }
        
        $this->command->info('Successfully seeded users. Total users: ' . User::count());
    }
}
